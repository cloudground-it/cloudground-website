/**
 * The installer's terminal: copy the command, and play the lines once it is on screen.
 *
 * What is copied is the `<code>`'s text, the same characters the reader sees. When the
 * Clipboard API is refused (an insecure origin, a denied permission) the command is
 * selected instead, so a Ctrl+C finishes the job, and the button does not claim a copy that
 * did not happen. The new label is also said through a status region: a button's changed
 * text is not announced by itself.
 */

const RESET_AFTER = 2000;

function select(el: Element): void {
    const range = document.createRange();
    range.selectNodeContents(el);
    const selection = window.getSelection();
    selection?.removeAllRanges();
    selection?.addRange(range);
}

export function initTerminal(root: ParentNode = document): void {
    for (const terminal of root.querySelectorAll<HTMLElement>("[data-terminal]")) {
        if (terminal.dataset.bound) continue;
        terminal.dataset.bound = "1";

        const button = terminal.querySelector<HTMLButtonElement>(".terminal-copy");
        const code = terminal.querySelector<HTMLElement>("[data-command]");
        const label = terminal.querySelector<HTMLElement>("[data-copy-label]");
        const what = terminal.querySelector<HTMLElement>("[data-copy-what]");
        const status = terminal.querySelector<HTMLElement>("[data-copy-status]");
        if (button && code && label) {
            const idle = label.textContent ?? "";
            let timer = 0;
            button.addEventListener("click", async () => {
                const command = (code.textContent ?? "").trim();
                try {
                    await navigator.clipboard.writeText(command);
                } catch {
                    select(code);
                    return;
                }
                const done = button.dataset.copied ?? idle;
                label.textContent = done;
                if (what) what.hidden = true;
                if (status) status.textContent = done;
                window.clearTimeout(timer);
                timer = window.setTimeout(() => {
                    label.textContent = idle;
                    if (what) what.hidden = false;
                    if (status) status.textContent = "";
                }, RESET_AFTER);
            });
        }

        // Only with motion: otherwise every line is already there and nothing waits.
        if (!document.documentElement.classList.contains("motion")) continue;
        const observer = new IntersectionObserver(
            (entries) => {
                if (entries.some((e) => e.isIntersecting)) {
                    terminal.classList.add("is-playing");
                    observer.disconnect();
                }
            },
            { rootMargin: "0px 0px -15% 0px" },
        );
        observer.observe(terminal);
    }
}
