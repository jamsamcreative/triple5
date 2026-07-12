#!/bin/bash
# Mid-turn reminder to update PROJECT_CONTEXT.md
# Counts source file edits, fires reminder every 5th edit
# Resets when PROJECT_CONTEXT.md is edited

INPUT=$(cat)
FILE_PATH=$(echo "$INPUT" | jq -r '.tool_input.file_path // .tool_response.filePath // empty')

[ -z "$FILE_PATH" ] && exit 0

# Reset counter when PROJECT_CONTEXT.md is edited
if echo "$FILE_PATH" | grep -q 'PROJECT_CONTEXT.md'; then
  rm -f /tmp/.claude-src-edit-count 2>/dev/null
  exit 0
fi

# Only count source file edits
echo "$FILE_PATH" | grep -qE '(src/|plugins/|lib/|app/|wp-content/)' || exit 0

COUNT_FILE="/tmp/.claude-src-edit-count"
COUNT=$(cat "$COUNT_FILE" 2>/dev/null || echo 0)
COUNT=$((COUNT + 1))
echo "$COUNT" > "$COUNT_FILE"

if [ $((COUNT % 5)) -eq 0 ]; then
  echo "{\"hookSpecificOutput\":{\"hookEventName\":\"PostToolUse\",\"additionalContext\":\"ACTION REQUIRED: You have made $COUNT source file edits without updating PROJECT_CONTEXT.md. Update it NOW before continuing with other work. Revise the Recent Focus Areas and Uncommitted Work sections to reflect what you have done so far this session. This is not optional.\"}}"
fi
