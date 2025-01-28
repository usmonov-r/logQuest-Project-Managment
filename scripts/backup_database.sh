#!/bin/bash

# Create backup directory if it doesn't exist
BACKUP_DIR="/Users/user/Documents/Programming/fullProjects/logQuest/backups"
mkdir -p "$BACKUP_DIR"

# Get current date for backup file name
DATE=$(date +"%Y%m%d_%H%M%S")

# Load environment variables from .env file
if [ -f ../.env ]; then
    export $(cat ../.env | grep -v '^#' | xargs)
fi

# Create backup
docker exec logQuest_db_c mysqldump \
    -u${MYSQL_USER} \
    -p${MYSQL_PASSWORD} \
    ${MYSQL_DATABASE} > "$BACKUP_DIR/backup_${DATE}.sql"

# Keep only last 7 backups
cd "$BACKUP_DIR"
ls -t | tail -n +8 | xargs -I {} rm -- {}

echo "Backup completed: backup_${DATE}.sql"
