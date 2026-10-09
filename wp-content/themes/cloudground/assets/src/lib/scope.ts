/**
 * The hero's oscilloscope: one switch, two states.
 *
 * The state lives in one attribute on the root (`data-cache="on|off"`); the stylesheet
 * shows each state's words and traces from it. This flips it, keeps `aria-pressed` on the
 * switch in step, and gives the screen its description for the new state — the picture is
 * `role="img"`, so its label is all a screen reader gets of it.
 */

export function initScope(root: ParentNode = document): void {
    for (const scope of root.querySelectorAll<HTMLElement>("[data-scope]")) {
        if (scope.dataset.bound) continue;
        scope.dataset.bound = "1";
        const toggle = scope.querySelector<HTMLButtonElement>(".scope-toggle");
        const screen = scope.querySelector<HTMLElement>(".scope-screen");
        toggle?.addEventListener("click", () => {
            const on = scope.dataset.cache !== "on";
            scope.dataset.cache = on ? "on" : "off";
            toggle.setAttribute("aria-pressed", String(on));
            const label = on ? scope.dataset.describeOn : scope.dataset.describeOff;
            if (screen && label) screen.setAttribute("aria-label", label);
        });
    }
}
