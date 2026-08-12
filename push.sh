#!/bin/bash
set -e

echo "🔒 Generating safe example files for Git..."

# 1. Create a safe inventory example
cp inventory.ini inventory.example.ini
sed -i '' -E 's/ansible_host=[0-9.]+/ansible_host=<YOUR_K3S_IP_HERE>/g' inventory.example.ini
sed -i '' -E 's/ansible_user=[a-zA-Z0-9_-]+/ansible_user=<YOUR_SSH_USER_HERE>/g' inventory.example.ini

# 2. Create a safe group_vars example
cp group_vars/all.yaml group_vars/all.example.yaml
sed -i '' -E 's/nextcloud_instance_id: .*/nextcloud_instance_id: "<YOUR_INSTANCE_ID>"/g' group_vars/all.example.yaml
sed -i '' -E 's/nextcloud_password_salt: .*/nextcloud_password_salt: "<YOUR_SALT>"/g' group_vars/all.example.yaml
sed -i '' -E 's/nextcloud_secret: .*/nextcloud_secret: "<YOUR_SECRET>"/g' group_vars/all.example.yaml
sed -i '' -E 's/minio_access_key: .*/minio_access_key: "<YOUR_MINIO_USER>"/g' group_vars/all.example.yaml
sed -i '' -E 's/minio_secret_key: .*/minio_secret_key: "<YOUR_MINIO_PASS>"/g' group_vars/all.example.yaml
sed -i '' -E 's/minio_endpoint: .*/minio_endpoint: "<YOUR_MINIO_ENDPOINT>"/g' group_vars/all.example.yaml

echo "✅ Safe example files generated."

# 3. Stage all non-ignored files
git add .

# 4. Commit (if there are changes)
COMMIT_MSG="${1:-Automated update}"
git commit -m "$COMMIT_MSG" || echo "No changes to commit."

# 5. Push to current branch (if a remote is configured)
if git remote -v | grep -q 'origin'; then
    git push origin HEAD
    echo "🚀 Successfully pushed to Git!"
else
    echo "⚠️  No git remote configured yet. Changes are committed locally."
    echo "To push, add a remote using: git remote add origin <URL>"
fi
