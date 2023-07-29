import ago from './ago';

const data = [
  { secs: 1, exp: '1 second', expSmall: '1 second' },
  { secs: 40, exp: '40 seconds', expSmall: '40 seconds' },
  {
    secs: 60 + 40,
    exp: '1 minute 40 seconds',
    expSmall: '1 minute 40 seconds',
  },
  { secs: 60, exp: '1 minute', expSmall: '1 minute' },
  {
    secs: 180 + 20,
    exp: '3 minutes 20 seconds',
    expSmall: '3 minutes 20 seconds',
  },
  { secs: 3600, exp: '1 hour', expSmall: '1 hour' },
  {
    secs: 3600 + 60 + 1,
    exp: '1 hour 1 minute 1 second',
    expSmall: '1 hour 1 minute',
  },
  {
    secs: 2 * 3600 + 120 + 20,
    exp: '2 hours 2 minutes 20 seconds',
    expSmall: '2 hours 2 minutes',
  },
  {
    secs: 25 * 3600 + 60 + 1,
    exp: '1 day 1 hour 1 minute 1 second',
    expSmall: '1 day 1 hour',
  },
  {
    secs: 25 * 3600 + 60,
    exp: '1 day 1 hour 1 minute',
    expSmall: '1 day 1 hour',
  },
  { secs: 24 * 3600 + 1, exp: '1 day 1 second', expSmall: '1 day 1 second' },
];
describe.each(data)('ago', ({ secs, exp, expSmall }) =>
  it(`${secs} seconds should be written as "${exp}" and "${expSmall}"`, () => {
    expect(ago(secs)).toBe(exp);
    expect(ago(secs, 2)).toBe(expSmall);
  }),
);
