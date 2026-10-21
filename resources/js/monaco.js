/*
 * Monaco is bundled with Dibi rather than fetched from a CDN at runtime.
 *
 * `@monaco-editor/loader` used to pull the editor from jsdelivr, which meant the
 * SQL query view silently rendered an empty pane whenever that host was blocked
 * or unreachable - a poor trade for a tool that otherwise only ever talks to a
 * local database.
 *
 * Only the SQL language and the editor features the query view actually uses are
 * registered here. The full `monaco-editor` entry point also ships the TypeScript,
 * HTML, CSS and JSON language services plus eighty-odd other grammars, which is
 * several megabytes Dibi has no use for.
 *
 * This module is imported dynamically so that it lands in its own chunk, and only
 * the SQL query view pays for it.
 */

import * as monaco from 'monaco-editor/editor/editor.api.js';

// Icon font for the find, suggest and folding widgets. Without it those widgets
// still render, but every glyph comes out as tofu.
import 'monaco-editor/features/codicon/register.js';

import 'monaco-editor/features/bracketMatching/register.js';
import 'monaco-editor/features/caretOperations/register.js';
import 'monaco-editor/features/clipboard/register.js';
import 'monaco-editor/features/comment/register.js';
import 'monaco-editor/features/contextmenu/register.js';
import 'monaco-editor/features/cursorUndo/register.js';
import 'monaco-editor/features/dnd/register.js';
import 'monaco-editor/features/dropOrPasteInto/register.js';
import 'monaco-editor/features/find/register.js';
import 'monaco-editor/features/folding/register.js';
import 'monaco-editor/features/fontZoom/register.js';
import 'monaco-editor/features/gotoLine/register.js';
import 'monaco-editor/features/inPlaceReplace/register.js';
import 'monaco-editor/features/indentation/register.js';
import 'monaco-editor/features/lineSelection/register.js';
import 'monaco-editor/features/linesOperations/register.js';
import 'monaco-editor/features/links/register.js';
import 'monaco-editor/features/longLinesHelper/register.js';
import 'monaco-editor/features/multicursor/register.js';
import 'monaco-editor/features/placeholderText/register.js';
import 'monaco-editor/features/quickCommand/register.js';
import 'monaco-editor/features/quickHelp/register.js';
import 'monaco-editor/features/readOnlyMessage/register.js';
import 'monaco-editor/features/smartSelect/register.js';
import 'monaco-editor/features/snippet/register.js';
import 'monaco-editor/features/suggest/register.js';
import 'monaco-editor/features/tokenization/register.js';
import 'monaco-editor/features/unicodeHighlighter/register.js';
import 'monaco-editor/features/wordHighlighter/register.js';
import 'monaco-editor/features/wordOperations/register.js';
import 'monaco-editor/features/wordPartOperations/register.js';

import 'monaco-editor/languages/definitions/sql/register.js';

import EditorWorker from 'monaco-editor/editor/editor.worker.js?worker';

self.MonacoEnvironment = {
    getWorker() {
        return new EditorWorker();
    },
};

export default monaco;
