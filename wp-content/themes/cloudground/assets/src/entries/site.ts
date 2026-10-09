/**
 * The home page, and any page built with widgets that ask for `site`.
 */
import "../css/site.css";
import { initReveal } from "../lib/reveal.ts";
import { initScope } from "../lib/scope.ts";
import { initTerminal } from "../lib/terminal.ts";
import { initMarquee } from "../lib/marquee.ts";
import { onElementorRender } from "../lib/editor.ts";

const init = (root: ParentNode = document) => {
    initReveal(root);
    initScope(root);
    initTerminal(root);
    initMarquee(root);
};

init();
onElementorRender((root) => init(root));
