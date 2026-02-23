param(
    [string]$TargetParent = 'C:\xampp\htdocs',
    [string]$ProjectName = 'uyayi-laravel'
)

function Write-ErrAndExit($msg){ Write-Host $msg -ForegroundColor Red; exit 1 }

# Check composer
$composer = (Get-Command composer -ErrorAction SilentlyContinue)
if(-not $composer){ Write-ErrAndExit "Composer not found in PATH. Install Composer and re-run this script." }

$targetPath = Join-Path $TargetParent $ProjectName
if(Test-Path $targetPath){ Write-Host "Target exists: $targetPath" -ForegroundColor Yellow }

# Create Laravel project
Write-Host "Creating Laravel project at $targetPath..."
composer create-project --prefer-dist laravel/laravel "$targetPath"
if($LASTEXITCODE -ne 0){ Write-ErrAndExit "composer failed. Check Composer output." }

# Copy scaffold files from current scaffold (assumes this script is inside c:\xampp\htdocs\uyayi\scripts)
$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Definition
$scaffoldRoot = Split-Path -Parent $scriptDir
Write-Host "Copying scaffold files from $scaffoldRoot to $targetPath (will overwrite existing files in target)"

# Exclude the scripts folder to avoid recursion
$items = Get-ChildItem -Path $scaffoldRoot -Force | Where-Object { $_.Name -ne 'scripts' }
foreach($it in $items){
    $dest = Join-Path $targetPath $it.Name
    if(Test-Path $dest){ Remove-Item -Recurse -Force $dest }
    Copy-Item -Path $it.FullName -Destination $targetPath -Recurse -Force
}

Write-Host "Done. Next steps:" -ForegroundColor Green
Write-Host "1) cd $targetPath"
Write-Host "2) copy .env.example to .env and set DB credentials"
Write-Host "3) php artisan key:generate"
Write-Host "4) Import database/schema.sql into MySQL or run migrations"
Write-Host "Example commands:" -ForegroundColor Cyan
Write-Host "   cd $targetPath"
Write-Host "   cp .env.example .env" 
Write-Host "   php artisan key:generate"
