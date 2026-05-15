// Mirrors PHP's abs(crc32($userId)) % 100 purely for display; the API owns the authoritative evaluation.
const TABLE = (() => {
  const t = new Int32Array(256);
  for (let i = 0; i < 256; i++) {
    let c = i;
    for (let j = 0; j < 8; j++) c = c & 1 ? (0xedb88320 ^ (c >>> 1)) : (c >>> 1);
    t[i] = c;
  }
  return t;
})();

function crc32(str: string): number {
  let crc = -1;
  for (let i = 0; i < str.length; i++) {
    crc = (crc >>> 8) ^ TABLE[(crc ^ str.charCodeAt(i)) & 0xff];
  }
  return (crc ^ -1) >>> 0; // unsigned, matching 64-bit PHP where crc32() never returns negative
}

export function rolloutBucket(userId: string): number {
  return crc32(userId) % 100;
}
