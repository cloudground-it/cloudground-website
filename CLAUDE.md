# CloudGround

A WordPress site built from [Bottega](https://github.com/frascella-dev/bottega):
- a **theme that draws** (`wp-content/themes/cloudground`);
- a **plugin that remembers and composes** (`wp-content/plugins/cloudground`);
- **Elementor** for the pages, **Polylang** for the languages.

## Project

<!-- Fill this in when the project starts (the bottega-new-project skill does). Only facts
     the owner gave: an empty line is better than a guess. -->

- **Business:** CloudGround, an open source hosting control panel
- **Languages:** it (default), en
- **Production:** not decided
- **Plugins it runs with:** Elementor, Polylang
- **Who receives the site's email:**
- **Laws that apply:**
- **Register of the words:**
- **Design:** `design/reference/home.dc.html` (the approved home, Italian copy)

## How to work here

- **Answer in the user's language.** Code, comments and docs are in English. The site's
  words are in its languages.
- **Read the guidelines before changing something of their kind:**
  - [principles](docs/guidelines/01-principles.md)
  - [plugin](docs/guidelines/02-plugin.md)
  - [theme](docs/guidelines/03-theme.md)
  - [Elementor](docs/guidelines/04-elementor.md)
  - [words and languages](docs/guidelines/05-words-and-languages.md)
  - [SEO](docs/guidelines/06-seo.md)
  - [accessibility](docs/guidelines/07-accessibility.md)
  - [privacy](docs/guidelines/08-privacy.md)
  - [testing](docs/guidelines/09-testing.md)
  - [release and deploy](docs/guidelines/10-release-and-deploy.md)
  - [code style](docs/guidelines/11-code-style.md)
- **Use the skills in `.claude/skills/`** for the usual jobs:
  - `bottega-new-project`
  - `elementor-section`
  - `elementor-widget`
  - `site-words`
  - `theme-builder-languages`
  - `plugin-in-theme`
  - `seo-head`
  - `privacy-notice`
  - `verify`
  - `release-deploy`

## The rules that are never bent

1. **Native first.** Words and pictures are Elementor's own widgets with the theme's
   classes. A plugin widget exists only where there is data or behaviour.
2. **No invented facts** about the business, and no invented reviews, numbers or claims.
3. **`/privacy` says what the code does**, and changes in the same release as the code.
4. **No real email from a development machine.** Everything goes to Mailpit, and tests use
   `@example.com` only. **On production nothing is ever submitted to test it**: checks
   there are read-only.
5. **Never print, upload or commit personal data or credentials.** Extract only what a task
   needs, delete it afterwards, and report a leaked password instead of using it.
6. **Verify before saying it works.** Run the tests, take screenshots and look at them,
   and report failures with their output.
7. **Outward-facing or hard-to-undo actions** (deploy, push, deleting data, sending) are
   confirmed with the user, unless they already said to go ahead for that action.

## Local site

```sh
bin/setup                                   # first run → http://localhost:8100 (admin / admin), mail → :8125
bin/wp <command>                            # WP-CLI
cd wp-content/themes/cloudground && npm run build  # after CSS / TS changes
cd wp-content/plugins/cloudground && npm test      # e2e: widgets, editor, languages, a11y
```
