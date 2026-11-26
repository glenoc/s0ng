const sharpNotes = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
const flatNotes = ['C', 'Db', 'D', 'Eb', 'E', 'F', 'Gb', 'G', 'Ab', 'A', 'Bb', 'B'];
const notes = { C: 0, D: 2, E: 4, F: 5, G: 7, A: 9, B: 11 };
const capoPattern = /^([ \t]*capo[ \t]*[:.]?[ \t]*)(\d+)(?:[ \t]*\.[ \t]*)?(?:st|nd|rd|th)?\.?(?:[ \t]*(?:fret|bånd))?/i;
const chordPattern = /^([A-G])([#b♯♭]?)((?:m|min|maj|M|dim|aug|sus|add|no|omit|alt|[0-9+#b♯♭°ø-]|\([^)]*\))*)(?:\/([A-G])([#b♯♭]?))?$/;
const sectionPattern = /\[(?:intro|outro|verse|vers|chorus|omkvæd|refrain|bridge|bro|break|interlude|mellemspil|instrumental|solo|pre[- ]chorus)(?:\s+\d+)?\]/gi;
const repeatPattern = /(^|[\s|,\[\]])\(\s*(?:x\s*\d+|\d+\s*x)\s*\)(?=$|[\s|,\[\]])/gi;

export function findCapo(lines) {
    for (let index = 0; index < lines.length; index += 1) {
        const match = lines[index].match(capoPattern);
        if (match) {
            return { index, fret: Number(match[2]) };
        }
    }
    return { index: -1, fret: 0 };
}

function transposeNote(note, accidental, semitones) {
    const alteration = accidental === '#' || accidental === '♯' ? 1 : accidental === 'b' || accidental === '♭' ? -1 : 0;
    const pitch = ((notes[note] + alteration + semitones) % 12 + 12) % 12;
    return (accidental === 'b' || accidental === '♭' ? flatNotes : sharpNotes)[pitch];
}

function chordToken(token) {
    let prefix = '';
    let suffix = '';
    while (token.startsWith('(') && token.endsWith(')')) {
        prefix += '(';
        suffix += ')';
        token = token.slice(1, -1);
    }
    return { prefix, suffix, token, chord: token.match(chordPattern) };
}

function pitch(note, accidental) {
    const alteration = accidental === '#' || accidental === '♯' ? 1 : accidental === 'b' || accidental === '♭' ? -1 : 0;
    return (notes[note] + alteration + 12) % 12;
}

export function parseChord(token) {
    const chord = token.match(chordPattern);
    if (!chord) {
        return null;
    }
    return {
        root: pitch(chord[1], chord[2]),
        quality: chord[3],
        bass: chord[4] ? pitch(chord[4], chord[5]) : pitch(chord[1], chord[2])
    };
}

function chordKey(chord) {
    return pitch(chord[1], chord[2]) + ':' + chord[3].replace(/^min/, 'm')
        + (chord[4] ? '/' + pitch(chord[4], chord[5]) : '');
}

export function extractChords(lines) {
    const chords = new Map();
    const unknown = [];
    lines.forEach(function (line) {
        if (capoPattern.test(line)) {
            return;
        }
        const notation = line.replace(sectionPattern, ' ').replace(repeatPattern, ' ');
        for (const match of notation.matchAll(/[^\s|,\[\]]+/g)) {
            const parsed = chordToken(match[0]);
            if (parsed.chord) {
                chords.set(chordKey(parsed.chord), chords.get(chordKey(parsed.chord)) || parsed.token);
            } else if (!/^[-.:/]+$/.test(match[0]) && !/^(?:N\.?C\.?|%)$/i.test(match[0])) {
                unknown.push(match[0]);
            }
        }
    });
    return { chords: Array.from(chords.values()), unknown };
}

export function assessPlayability(lines, knownChords, settings = {}, maxCapo = 5) {
    const originalCapo = findCapo(lines).fret;
    const semitones = settings.semitones ?? 0;
    const currentCapo = settings.capo ?? originalCapo;
    const { chords, unknown } = extractChords(lines);
    if (unknown.length || !chords.length || originalCapo > 24) {
        return { status: 'unknown' };
    }
    if (!knownChords.length) {
        return { status: 'no-chords' };
    }
    const known = new Set(knownChords.map(chord => chord.match(chordPattern)).filter(Boolean).map(chordKey));
    const playable = shift => chords.every(chord => known.has(chordKey(transposeChordLine(chord, shift).match(chordPattern))));
    if (playable(semitones + originalCapo - currentCapo)) {
        return { status: 'playable' };
    }
    const candidates = Array.from({ length: maxCapo + 1 }, (_, fret) => fret)
        .filter(fret => fret !== currentCapo)
        .sort((a, b) => Math.abs(a - currentCapo) - Math.abs(b - currentCapo) || a - b);
    for (const fret of candidates) {
        if (playable(semitones + originalCapo - fret)) {
            return { status: 'capo', fret };
        }
    }
    return { status: 'unplayable' };
}

export function transposeChordLine(line, semitones) {
    if (semitones % 12 === 0) {
        return line;
    }

    return line.replace(/([^\s|,\[\]]+)([ \t]*)/g, function (match, token, spaces, offset) {
        const parsed = chordToken(token);
        const chord = parsed.chord;
        if (!chord) {
            return match;
        }

        const transposed = parsed.prefix + transposeNote(chord[1], chord[2], semitones) + (chord[3] || '')
            + (chord[4] ? '/' + transposeNote(chord[4], chord[5], semitones) : '') + parsed.suffix;
        if (parsed.suffix || !spaces || offset + match.length === line.length || !/^ +$/.test(spaces)) {
            return transposed + spaces;
        }
        return transposed + ' '.repeat(Math.max(1, spaces.length + token.length - transposed.length));
    });
}

export function formatCapoLine(line, fret) {
    return line.replace(capoPattern, function (match, prefix) {
        return prefix + fret;
    });
}
