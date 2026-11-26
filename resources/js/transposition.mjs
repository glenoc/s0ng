const sharpNotes = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
const flatNotes = ['C', 'Db', 'D', 'Eb', 'E', 'F', 'Gb', 'G', 'Ab', 'A', 'Bb', 'B'];
const notes = { C: 0, D: 2, E: 4, F: 5, G: 7, A: 9, B: 11, H: 11 };
const capoPattern = /^([ \t]*capo[ \t]*[:.]?[ \t]*)(\d+)(?:[ \t]*\.[ \t]*)?(?:st|nd|rd|th)?\.?(?:[ \t]*(?:fret|bånd))?/i;
const chordPattern = /^([A-GH])([#b♯♭]?)((?:min|maj|m|M|dim|aug|sus|add|no|omit|alt|[0-9+#b♯♭°øo-]|\([^)]*\))*)(?:\/([A-GHa-gh])([#b♯♭]?))?$/;
const chordSequencePattern = new RegExp(chordPattern.source.slice(1, -1) + "([*'’!`]*)", 'gy');
const sectionPattern = /\[(?:intro|outro|verse|vers|chorus|omkvæd|refrain|bridge|bro|break|interlude|mellemspil|instrumental|solo|harmonies|pre[- ]chorus)(?:\s+\d+)?\]/gi;
const repeatPattern = /(^|[\s|,\[\]])(?:\(\s*(?:x\s*\d+|\d+\s*x)\s*\)|x\s*\d+|\d+\s*x)(?=$|[\s|,\[\]])/gi;
const directionPattern = /\(\s*(?:break|fade\s+out)\s*\)/gi;
const notationExplanationPattern = /\(\s*['’*!=%]\s*=\s*[^)]*\)/g;

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
    const pitch = ((notes[note.toUpperCase()] + alteration + semitones) % 12 + 12) % 12;
    return (accidental === 'b' || accidental === '♭' ? flatNotes : sharpNotes)[pitch];
}

function chordToken(token) {
    let prefix = '';
    const decoration = token.match(/[*'’!`]+$/)?.[0] || '';
    let suffix = decoration;
    token = token.slice(0, token.length - decoration.length);
    while (token.startsWith('(') && token.endsWith(')')) {
        prefix += '(';
        suffix = ')' + suffix;
        token = token.slice(1, -1);
    }
    const parts = [];
    chordSequencePattern.lastIndex = 0;
    while (chordSequencePattern.lastIndex < token.length) {
        const chord = chordSequencePattern.exec(token);
        if (!chord) {
            return { prefix, suffix, parts: [] };
        }
        parts.push({ chord, token: chord[0].slice(0, chord[0].length - chord[6].length), decoration: chord[6] });
    }
    return { prefix, suffix, parts };
}

function isInstructionLine(line) {
    if (/^\s*original\s+key\s*[:.]?\s*[A-G][#b♯♭]?(?:m|min|maj)?\s*$/i.test(line)
        || /^\s*\(\s*transpose\b[^)]*\)\s*$/i.test(line)
        || /^\s*tuning\s*:\s*standard\s*$/i.test(line)
        || /^\s*(?:intro|solo)\s*:?\s*$/i.test(line)) {
        return true;
    }
    if (!/^\s*(?:[A-Za-z][a-z]+[ \t]+){3,}[A-Za-z][a-z]+[.!?]?\s*$/.test(line)) {
        return false;
    }
    return line.trim().replace(/[.!?]$/, '').split(/\s+/).every(word => !chordPattern.test(word));
}

function pitch(note, accidental) {
    const alteration = accidental === '#' || accidental === '♯' ? 1 : accidental === 'b' || accidental === '♭' ? -1 : 0;
    return (notes[note.toUpperCase()] + alteration + 12) % 12;
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
    return pitch(chord[1], chord[2]) + ':' + chord[3].replace(/^min/, 'm').replace(/^(?:o(?=$|[0-9(])|°)/, 'dim')
        + (chord[4] ? '/' + pitch(chord[4], chord[5]) : '');
}

export function extractChords(lines) {
    const chords = new Map();
    const unknown = [];
    lines.forEach(function (line) {
        if (capoPattern.test(line) || isInstructionLine(line)) {
            return;
        }
        const notation = line.replace(sectionPattern, ' ').replace(repeatPattern, '$1 ')
            .replace(directionPattern, ' ').replace(notationExplanationPattern, ' ');
        for (const match of notation.matchAll(/[^\s|,\[\]]+/g)) {
            const parsed = chordToken(match[0]);
            if (parsed.parts.length) {
                parsed.parts.forEach(function (part) {
                    chords.set(chordKey(part.chord), chords.get(chordKey(part.chord)) || part.token);
                });
            } else if (!/^[-.:/!_]+$/.test(match[0]) && !/^(?:N\.?C\.?|%|break)$/i.test(match[0])) {
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
    if (semitones % 12 === 0 || isInstructionLine(line)) {
        return line;
    }

    const explanations = Array.from(line.matchAll(notationExplanationPattern), match => [match.index, match.index + match[0].length]);
    return line.replace(/([^\s|,\[\]]+)([ \t]*)/g, function (match, token, spaces, offset) {
        if (explanations.some(([start, end]) => offset >= start && offset < end)) {
            return match;
        }
        const parsed = chordToken(token);
        if (!parsed.parts.length) {
            return match;
        }

        const transposed = parsed.prefix + parsed.parts.map(function (part) {
            const chord = part.chord;
            return transposeNote(chord[1], chord[2], semitones) + (chord[3] || '')
                + (chord[4] ? '/' + transposeNote(chord[4], chord[5], semitones) : '') + part.decoration;
        }).join('') + parsed.suffix;
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
