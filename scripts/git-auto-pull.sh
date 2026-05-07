#!/bin/bash
# Script Auto Pull Background
# Berguna untuk CI/CD sederhana di VPS
# Cara menjalankan: nohup ./scripts/git-auto-pull.sh > git-pull.log 2>&1 &

# Masuk ke root direktori project
cd "$(dirname "$0")/.." || exit

BRANCH="main"

echo "Memantau perubahan Git di branch $BRANCH..."

while true; do
    # Fetch data terbaru dari remote
    git fetch origin $BRANCH
    
    LOCAL=$(git rev-parse HEAD)
    REMOTE=$(git rev-parse origin/$BRANCH)

    if [ "$LOCAL" != "$REMOTE" ]; then
        echo "$(date): Commit baru terdeteksi. Pulling dari $BRANCH..."
        git pull origin $BRANCH
        
        # Eksekusi post-pull commands jika ada, misalnya build asset atau restart service
        # sudo systemctl restart nginx
        
        echo "$(date): Auto-pull selesai."
    fi
    
    # Cek setiap 60 detik
    sleep 60
done
