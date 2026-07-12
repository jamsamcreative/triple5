#!/bin/bash
LOCK_DIR="/tmp/.claude-file-locks"
INPUT=$(cat)
FILE_PATH=$(echo "$INPUT" | jq -r '.tool_input.file_path // empty')
SESSION_ID=$(echo "$INPUT" | jq -r '.session_id // empty')
if [ -z "$FILE_PATH" ]; then exit 0; fi
LOCK_NAME=$(echo "$FILE_PATH" | md5 -q 2>/dev/null || echo "$FILE_PATH" | md5sum | cut -d' ' -f1)
LOCK_PATH="$LOCK_DIR/$LOCK_NAME"
LOCK_FILE="$LOCK_PATH/lock"
if [ -f "$LOCK_FILE" ]; then
  LOCK_SESSION=$(head -1 "$LOCK_FILE" 2>/dev/null)
  if [ "$LOCK_SESSION" = "$SESSION_ID" ]; then
    rm -f "$LOCK_FILE"; rmdir "$LOCK_PATH" 2>/dev/null
  fi
fi
