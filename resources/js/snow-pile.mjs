export function createSnowPile(width) {
    return new Float32Array(Math.max(1, Math.ceil(width / 14)));
}

export function createSnowProfile(length, random = Math.random) {
    const profile = new Float32Array(length);
    let left = random() * 12;
    let right = random() * 12;

    for (let index = 0; index < length; index += 1) {
        if (index > 0 && index % 5 === 0) {
            left = right;
            right = random() * 12;
        }
        profile[index] = left + (right - left) * (index % 5) / 5;
    }

    return profile;
}

export function addSnow(pile, index, amount, maxHeight) {
    pile[index] = Math.min(maxHeight, pile[index] + amount);
}

export function pushSnow(pile, from, to, capacity, load = 0) {
    const direction = Math.sign(to - from);

    if (!direction) {
        return load;
    }

    for (let index = from; index !== to; index += direction) {
        const moved = Math.min(pile[index], capacity - load);
        pile[index] -= moved;
        load += moved;
    }

    if ((direction === 1 && to === pile.length - 1) || (direction === -1 && to === 0)) {
        const moved = Math.min(pile[to], capacity - load);
        pile[to] -= moved;
        return 0;
    }

    return load;
}

export function depositSnow(pile, index, load, maxHeight) {
    for (let distance = 0; distance < pile.length && load > 0; distance += 1) {
        const positions = distance === 0 ? [index] : [index - distance, index + distance];
        for (const position of positions) {
            if (position < 0 || position >= pile.length) {
                continue;
            }
            const moved = Math.min(load, maxHeight - pile[position]);
            pile[position] += moved;
            load -= moved;
        }
    }

    return load;
}
