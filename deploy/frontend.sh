#!/bin/bash
# Deploy frontend (React SPA) to production

set -e

echo "🚀 Deploying Frontend..."

# Configuration
cd "$(dirname "$0")/../frontend"

# Install dependencies and build
npm ci
npm run build

# Deploy to server (adjust based on your hosting)
# Option 1: SCP to server
# scp -r dist/* user@server:/var/www/sms_erp/frontend/public/

# Option 2: AWS S3 (uncomment and configure)
# aws s3 sync dist/ s3://your-bucket-name --delete

# Option 3: Vercel (uncomment and configure)
# vercel --prod

echo "✅ Frontend deployed successfully!"
