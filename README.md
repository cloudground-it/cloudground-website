# CloudGround

The website of CloudGround, on WordPress with Elementor and Polylang, built from
[Bottega](https://github.com/frascella-dev/bottega).

| | |
|---|---|
| `wp-content/themes/cloudground` | **The look.** Templates, CSS and motion (built with Vite), the words per language in `inc/content/`. |
| `wp-content/plugins/cloudground` | **The rest.** The facts (Settings → CloudGround), the Elementor widgets, the dynamic tags, the Kit, the e2e tests. |
| `wp-content/mu-plugins/local-mail.php` | Locally, no email leaves: everything goes to Mailpit. |
| `docs/guidelines/` | How the site is built, and why. |
| `.claude/` | The Claude Code skills and agents for working on it. |

## Local site

```sh
bin/setup                                    # → http://localhost:8100 (admin / admin), mail → http://localhost:8125
bin/wp <command>                             # WP-CLI
cd wp-content/themes/cloudground && npm run build   # after CSS or TypeScript changes
cd wp-content/plugins/cloudground && npm test       # widgets, editor, languages, accessibility
```

## Release

See [`docs/guidelines/10-release-and-deploy.md`](docs/guidelines/10-release-and-deploy.md):
1. the version in three places;
2. `CHANGELOG.md`;
3. `bin/build-zips`;
4. a GitHub release;
5. install on production with WP-CLI.

## Two rules

- **`/privacy` says what the code does.** When what the site collects changes, that page
  changes in the same release.
- **No invented facts about the business.**
