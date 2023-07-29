const addUnit = (amount: number, unit: string) => {
  if (amount === 0) {
    return null;
  }
  if (amount === 1) {
    return `${amount} ${unit}`;
  }
  return `${amount} ${unit}s`;
};

const ago = (seconds: number, numberOfUnit: number = 4): string => {
  const numDays = Math.floor(seconds / 86400);
  const numHours = Math.floor((seconds % 86400) / 3600);
  const numMinutes = Math.floor(((seconds % 86400) % 3600) / 60);
  const numSeconds = (((seconds % 31536000) % 86400) % 3600) % 60;
  return [
    addUnit(numDays, 'day'),
    addUnit(numHours, 'hour'),
    addUnit(numMinutes, 'minute'),
    addUnit(numSeconds, 'second'),
  ]
    .filter((s) => s)
    .slice(0, numberOfUnit)
    .join(' ')
    .trim();
};
export default ago;
