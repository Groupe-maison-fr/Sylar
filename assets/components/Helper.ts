export type ArrElement<ArrType> = ArrType extends readonly (infer ElementType)[]
  ? ElementType
  : never;

export type Values<T> = T[keyof T];

export type AnyEnum<Values extends string | number = string | number> =
  Readonly<Record<string, Values>>;

export function getOffsetValue<Enum extends AnyEnum>(
  e: Enum,
  current: Values<Enum>,
  distance: number,
): Values<Enum> {
  const values = Object.values(e) as Values<Enum>[];

  // You could just do this:
  // const index = (values.indexOf(current) + distance) % values.length;

  let index = values.indexOf(current);
  // But it's safer to validate at runtime:
  if (index === -1) throw new TypeError('Value not found');
  index = (index + distance) % values.length;
  return values[index < 0 ? values.length + index : index]!;
}

export function getNextValue<Enum extends AnyEnum>(
  e: Enum,
  current: Values<Enum>,
): Values<Enum> {
  return getOffsetValue(e, current, 1);
}

export function getPreviousValue<Enum extends AnyEnum>(
  e: Enum,
  current: Values<Enum>,
): Values<Enum> {
  return getOffsetValue(e, current, -1);
}
