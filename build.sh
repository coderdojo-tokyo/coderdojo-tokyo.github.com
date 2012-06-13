#!/bin/sh
echo "----------------------------------------" >> logs/git-pull.log
LANG=en date >> logs/git-pull.log
git pull >> logs/git-pull.log
