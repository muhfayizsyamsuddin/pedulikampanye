import { marked } from 'marked';
import DOMPurify from 'dompurify';

marked.setOptions({
    breaks: true,
    gfm: true,
});

window.renderMarkdown = (content) =>
    DOMPurify.sanitize(marked.parse(content));