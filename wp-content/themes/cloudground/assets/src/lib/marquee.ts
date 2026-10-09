/**
 * The marquee of technologies: its words written once, in native widgets, and doubled here
 * so the loop has no seam — the track slides by half its width, which is one copy.
 *
 * Only with motion: a still marquee needs no second copy. The copy is `aria-hidden` and
 * `inert`, so the words are read once and nothing in it takes the focus.
 */

export function initMarquee(root: ParentNode = document): void {
    if (!document.documentElement.classList.contains("motion")) return;
    for (const track of root.querySelectorAll<HTMLElement>(".marquee-track")) {
        if (track.dataset.looped) continue;
        const copies = [...track.children].map((child) => {
            const copy = child.cloneNode(true) as HTMLElement;
            copy.setAttribute("aria-hidden", "true");
            copy.inert = true;
            return copy;
        });
        track.append(...copies);
        track.dataset.looped = "1";
    }
}
