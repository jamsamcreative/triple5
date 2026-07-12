#!/bin/bash
# Conditionally inject DESIGN_CONTEXT.md into the prompt context.
# Triggers when:
#   1. User prompt mentions design/UI keywords, OR
#   2. Recently modified files (last 30 min) include frontend extensions, OR
#   3. The prompt contains a Figma URL
# Stays silent otherwise so backend-only sessions remain lean.

PROJECT_ROOT_PLACEHOLDER="/Users/jamiesampson/.superset/projects/triple5"
DESIGN_FILE="$PROJECT_ROOT_PLACEHOLDER/DESIGN_CONTEXT.md"

[ ! -f "$DESIGN_FILE" ] && exit 0

INPUT=$(cat)
PROMPT=$(echo "$INPUT" | jq -r '.prompt // empty' 2>/dev/null)

PROMPT_LC=$(echo "$PROMPT" | tr '[:upper:]' '[:lower:]')

KEYWORD_MATCH=0
echo "$PROMPT_LC" | grep -qE '(design|style|css|tailwind|theme|color|font|typography|spacing|layout|ui|ux|component|button|modal|nav|hero|landing|figma|frontend|responsive|mobile|desktop|dark mode|light mode|animation|motion|hover|focus|accessibility|a11y|wcag|contrast|cornerstone|pro theme|block theme|gutenberg)' && KEYWORD_MATCH=1

FILE_MATCH=0
if [ -d "$PROJECT_ROOT_PLACEHOLDER" ]; then
  RECENT=$(find "$PROJECT_ROOT_PLACEHOLDER" \
    -type f \
    \( -name "*.tsx" -o -name "*.jsx" -o -name "*.vue" -o -name "*.svelte" \
       -o -name "*.css" -o -name "*.scss" -o -name "*.sass" -o -name "*.less" \
       -o -name "*.html" -o -name "*.php" -o -name "tailwind.config.*" -o -name "*.tokens.*" \) \
    -mmin -30 \
    -not -path "*/node_modules/*" \
    -not -path "*/.next/*" \
    -not -path "*/dist/*" \
    -not -path "*/build/*" \
    2>/dev/null | head -1)
  [ -n "$RECENT" ] && FILE_MATCH=1
fi

if [ "$KEYWORD_MATCH" -eq 0 ] && [ "$FILE_MATCH" -eq 0 ]; then
  exit 0
fi

CTX=$(cat "$DESIGN_FILE" 2>/dev/null)
[ -z "$CTX" ] && exit 0

jq -n --arg ctx "$CTX" '{hookSpecificOutput:{hookEventName:"UserPromptSubmit",additionalContext:("DESIGN CONTEXT (loaded because this prompt or recent work involves UI/design). You MUST follow DESIGN_CONTEXT.md and update it whenever you make a design decision (new color, new pattern, new component convention, new motion rule, etc.). Do not wait until end of session. Here is the current content:\n\n" + $ctx)}}'
