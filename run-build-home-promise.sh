#!/usr/bin/env bash
cd /home/lucas/lucas/yv_wellness || exit 1
npx wp-scripts build \
  --webpack-src-dir=src/wp-content/themes/ai-zippy-child/src/blocks \
  --output-path=src/wp-content/themes/ai-zippy-child/assets/blocks 2>&1 | tail -120
