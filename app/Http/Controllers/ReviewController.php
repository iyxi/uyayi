<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Product $product, Request $request)
    {
        $query = Review::with('user')
            ->where('reviewable_type', Product::class)
            ->where('reviewable_id', $product->id)
            ->latest();

        $reviews = $query->get()->map(function ($review) {
            return [
                'id' => $review->id,
                'rating' => (int) $review->rating,
                'comment' => $review->comment,
                'created_at' => optional($review->created_at)->toDateTimeString(),
                'user' => [
                    'id' => $review->user?->id,
                    'name' => $review->user?->name ?? 'Anonymous',
                ],
            ];
        })->values();

        $average = round((float) $reviews->avg('rating'), 2);
        $count = (int) $reviews->count();

        $canReview = false;
        $userReview = null;

        if ($request->user()) {
            $canReview = $this->userHasPurchasedProduct($request->user()->id, $product->id);

            $existing = Review::where('user_id', $request->user()->id)
                ->where('reviewable_type', Product::class)
                ->where('reviewable_id', $product->id)
                ->first();

            if ($existing) {
                $userReview = [
                    'id' => $existing->id,
                    'rating' => (int) $existing->rating,
                    'comment' => $existing->comment,
                ];
            }
        }

        return response()->json([
            'reviews' => $reviews,
            'summary' => [
                'average_rating' => $average,
                'count' => $count,
            ],
            'can_review' => $canReview,
            'user_review' => $userReview,
        ]);
    }

    public function upsert(Product $product, Request $request)
    {
        $user = $request->user();

        if (!$this->userHasPurchasedProduct($user->id, $product->id)) {
            return response()->json([
                'message' => 'Only customers who bought this product can review it.',
            ], 403);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review = Review::updateOrCreate(
            [
                'user_id' => $user->id,
                'reviewable_type' => Product::class,
                'reviewable_id' => $product->id,
            ],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
            ]
        );

        return response()->json([
            'message' => 'Review saved successfully.',
            'review' => [
                'id' => $review->id,
                'rating' => (int) $review->rating,
                'comment' => $review->comment,
            ],
        ]);
    }

    private function userHasPurchasedProduct(int $userId, int $productId): bool
    {
        return OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.user_id', $userId)
            ->where('orders.status', 'Completed')
            ->where('order_items.product_id', $productId)
            ->exists();
    }
}
