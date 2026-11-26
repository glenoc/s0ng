import { parseChord } from './transposition.mjs';

const tuning = [4, 9, 2, 7, 11, 4];
const intervals = {
    '': [0, 4, 7], m: [0, 3, 7], min: [0, 3, 7],
    5: [0, 7], 6: [0, 4, 7, 9], m6: [0, 3, 7, 9],
    7: [0, 4, 7, 10], m7: [0, 3, 7, 10], min7: [0, 3, 7, 10],
    maj7: [0, 4, 7, 11], M7: [0, 4, 7, 11],
    sus2: [0, 2, 7], sus4: [0, 5, 7], sus: [0, 5, 7],
    add9: [0, 4, 7, 2], madd9: [0, 3, 7, 2],
    dim: [0, 3, 6], dim7: [0, 3, 6, 9], aug: [0, 4, 8]
};
const simpleVoicings = {
    '0:': [-1, 3, 2, 0, 1, 0],
    '2:': [-1, -1, 0, 2, 3, 2],
    '4:': [0, 2, 2, 1, 0, 0],
    '5:': [-1, -1, 3, 2, 1, 1],
    '7:': [3, 2, 0, 0, 0, 3],
    '9:': [-1, 0, 2, 2, 2, 0],
    '11:': [-1, 2, 4, 4, 4, 2],
    '2:m': [-1, -1, 0, 2, 3, 1],
    '4:m': [0, 2, 2, 0, 0, 0],
    '5:m': [-1, -1, 3, 1, 1, 1],
    '6:m': [-1, -1, 4, 2, 2, 2],
    '9:m': [-1, 0, 2, 2, 1, 0],
    '11:m': [-1, 2, 4, 4, 3, 2],
    '2:7': [-1, -1, 0, 2, 1, 2],
    '4:7': [0, 2, 0, 1, 0, 0],
    '9:7': [-1, 0, 2, 0, 2, 0]
};

export function guitarDiagramStart(frets) {
    const fretted = frets.filter(fret => fret > 0);
    return !fretted.length || Math.max(...fretted) <= 4 ? 1 : Math.min(...fretted);
}

export function guitarVoicing(symbol) {
    const chord = parseChord(symbol);
    if (!chord) {
        return null;
    }
    const normalizedQuality = chord.quality.replace(/[()]/g, '').replace(/^(?:o(?=$|[0-9(])|°)/, 'dim');
    const formula = intervals[normalizedQuality];
    if (!formula) {
        return null;
    }
    const quality = normalizedQuality.replace(/^min/, 'm');
    const simple = chord.bass === chord.root && simpleVoicings[chord.root + ':' + quality];
    if (simple) {
        return simple.slice();
    }
    const required = new Set(formula.map(interval => (chord.root + interval) % 12));
    const allowed = new Set([...required, chord.bass]);
    let best = null;
    let bestScore = Infinity;

    for (let start = 1; start <= 12; start += 1) {
        const choices = tuning.map(open => [-1, 0, start, start + 1, start + 2, start + 3]
            .filter(fret => fret === -1 || (fret <= 12 && allowed.has((open + fret) % 12))));

        function search(string, frets, sounded, fingers) {
            if (fingers > 4) {
                return;
            }
            if (string === tuning.length) {
                if (frets.filter(fret => fret >= 0).length < (required.size === 2 ? 2 : 3)
                    || [...required].some(note => !sounded.has(note))) {
                    return;
                }
                const firstPlayed = frets.findIndex(fret => fret >= 0);
                const lastPlayed = frets.length - 1 - frets.slice().reverse().findIndex(fret => fret >= 0);
                const internalMutes = frets.slice(firstPlayed, lastPlayed + 1).filter(fret => fret < 0).length;
                const score = frets.filter(fret => fret < 0).length * 2 + internalMutes * 8
                    + frets.reduce((sum, fret) => sum + Math.max(fret, 0), 0) * 0.45
                    + Math.max(...frets, 0) * 1.5;
                if (score < bestScore) {
                    best = frets.slice();
                    bestScore = score;
                }
                return;
            }
            for (const fret of choices[string]) {
                const note = fret < 0 ? null : (tuning[string] + fret) % 12;
                if (note !== null && sounded.size === 0 && note !== chord.bass) {
                    continue;
                }
                if (note !== null && note !== chord.bass && !required.has(note)) {
                    continue;
                }
                frets.push(fret);
                const next = new Set(sounded);
                if (note !== null) {
                    next.add(note);
                }
                search(string + 1, frets, next, fingers + (fret > 0 ? 1 : 0));
                frets.pop();
            }
        }
        search(0, [], new Set(), 0);
    }
    return best;
}
