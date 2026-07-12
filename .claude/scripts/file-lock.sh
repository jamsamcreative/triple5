#!/bin/bash
LOCK_DIR="/tmp/.claude-file-locks"
STALE_SECONDS=120
mkdir -p "$LOCK_DIR"
INPUT=$(cat)
FILE_PATH=$(echo "$INPUT" | jq -r '.tool_input.file_path // empty')
SESSION_ID=$(echo "$INPUT" | jq -r '.session_id // empty')
if [ -z "$FILE_PATH" ]; then exit 0; fi
LOCK_NAME=$(echo "$FILE_PATH" | md5 -q 2>/dev/null || echo "$FILE_PATH" | md5sum | cut -d' ' -f1)
LOCK_PATH="$LOCK_DIR/$LOCK_NAME"
LOCK_FILE="$LOCK_PATH/lock"
if [ -d "$LOCK_PATH" ]; then
  if [ -f "$LOCK_FILE" ]; then
    LOCK_SESSION=$(head -1 "$LOCK_FILE" 2>/dev/null)
    LOCK_TIME=$(tail -1 "$LOCK_FILE" 2>/dev/null)
    NOW=$(date +%s)
    AGE=$(( NOW - LOCK_TIME ))
    if [ "$LOCK_SESSION" = "$SESSION_ID" ]; then
      echo "$SESSION_ID" > "$LOCK_FILE"; echo "$NOW" >> "$LOCK_FILE"; exit 0
    fi
    if [ "$AGE" -gt "$STALE_SECONDS" ]; then
      echo "$SESSION_ID" > "$LOCK_FILE"; echo "$NOW" >> "$LOCK_FILE"; exit 0
    fi
    BASENAME=$(basename "$FILE_PATH")
    echo "{\"hookSpecificOutput\":{\"hookEventName\":\"PreToolUse\",\"permissionDecision\":\"deny\",\"permissionDecisionReason\":\"File '$BASENAME' is being edited by another Claude session (locked ${AGE}s ago). Wait and retry, or work on a different file.\"}}"
    exit 0
  fi
fi
if mkdir "$LOCK_PATH" 2>/dev/null; then
  echo "$SESSION_ID" > "$LOCK_FILE"; echo "$(date +%s)" >> "$LOCK_FILE"
else
  BASENAME=$(basename "$FILE_PATH")
  echo "{\"hookSpecificOutput\":{\"hookEventName\":\"PreToolUse\",\"permissionDecision\":\"deny\",\"permissionDecisionReason\":\"File '$BASENAME' was just locked by another Claude session. Wait and retry.\"}}"
fi
