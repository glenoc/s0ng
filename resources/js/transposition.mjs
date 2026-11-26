const sharpNotes = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
const flatNotes = ['C', 'Db', 'D', 'Eb', 'E', 'F', 'Gb', 'G', 'Ab', 'A', 'Bb', 'B'];
const notes = { C: 0, D: 2, E: 4, F: 5, G: 7, A: 9, B: 11 };
const capoPattern = /^([ \t]*capo[ \t]*[:.]?[ \t]*)(\d+)(?:[ \t]*\.[ \t]*)?(?:st|nd|rd|th)?\.?(?:[ \t]*(?:fret|bånd))?/i;
const chordPattern = /^([A-G])([#b♯♭]?)((?:m|min|maj|M|dim|aug|sus|add|no|omit|alt|[0-9+#b♯♭°ø-]|\([^)]*\))*)(?:\/([A-G])([#b♯♭]?))?$/;

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

export function transposeChordLine(line, semitones) {
    if (semitones % 12 === 0) {
        return line;
    }

    return line.replace(/([^\s|()[\],]+)([ \t]*)/g, function (match, token, spaces, offset) {
        const chord = token.match(chordPattern);
        if (!chord) {
            return match;
        }

        const transposed = transposeNote(chord[1], chord[2], semitones) + (chord[3] || '')
            + (chord[4] ? '/' + transposeNote(chord[4], chord[5], semitones) : '');
        if (!spaces || offset + match.length === line.length || !/^ +$/.test(spaces)) {
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
