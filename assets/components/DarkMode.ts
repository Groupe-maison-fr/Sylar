// @ts-ignore
const setInitialDarkMode = (prefersDarkMode) => {
  localStorage.setItem('prefersDarkMode', JSON.stringify(prefersDarkMode));
};

const initialDarkMode = () => {
  if (localStorage.getItem('prefersDarkMode') === null) {
    setInitialDarkMode(
      window.matchMedia('(prefers-color-scheme: dark)').matches,
    );
  }
  return JSON.parse(localStorage.getItem('prefersDarkMode') ?? '');
};

export { initialDarkMode, setInitialDarkMode };
