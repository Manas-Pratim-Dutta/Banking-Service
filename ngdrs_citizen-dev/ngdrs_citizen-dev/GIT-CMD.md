# Git Commands Reference

A comprehensive guide to useful Git commands with explanations and use cases.

## Common Commands

```bash
# delete branch locally
git branch -d feature/new-feature
# use when: work done on the temporary branch has been merged to main

# delete repo branch
git push origin --delete feature/new-feature
# use when: work done on the temporary branch has been merged to main
```

**Pro Tips:**
- Always `git pull` before starting new work
- Write clear, descriptive commit messages
- Commit often, push regularly
- Never force push to shared branches without team agreement
- Use `.gitignore` to exclude unwanted files
- Review changes with `git diff` before committing