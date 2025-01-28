#!/bin/bash

BACKUP_DIR="/Users/user/Documents/Programming/fullProjects/logQuest/backups"

# Check if backup file is provided
if [ -z "$1" ]; then
    echo "Please provide the backup file name"
    echo "Available backups:"
    ls -1 "$BACKUP_DIR"
    exit 1
fi

# Load environment variables from .env file
if [ -f ../.env ]; then
    export $(cat ../.env | grep -v '^#' | xargs)
fi

# Check if backup file exists
BACKUP_FILE="$BACKUP_DIR/$1"
if [ ! -f "$BACKUP_FILE" ]; then
    echo "Backup file not found: $BACKUP_FILE"
    exit 1
fi

# Restore backup
echo "Restoring backup from: $1"
docker exec -i logQuest_db_c mysql \
    -u${MYSQL_USER} \
    -p${MYSQL_PASSWORD} \
    ${MYSQL_DATABASE} < "$BACKUP_FILE"

echo "Restore completed!"
