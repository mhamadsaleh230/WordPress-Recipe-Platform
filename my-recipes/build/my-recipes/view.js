/******/ (() => { // webpackBootstrap
/*!********************************!*\
  !*** ./src/my-recipes/view.js ***!
  \********************************/
const searchInput = document.querySelector('#recipe-search');
const cuisineInput = document.querySelector('#recipe-cuisine');
const ingredientInput = document.querySelector('#recipe-ingredient');
const defaultContainer = document.querySelector('#default-recipes');
const searchResults = document.querySelector('#search-results');

// Attach debounce to all inputs
if (searchInput || cuisineInput || ingredientInput) {
  const debouncedSearch = debounce(handleSearch, 500);
  if (searchInput) searchInput.addEventListener('input', debouncedSearch);
  if (cuisineInput) cuisineInput.addEventListener('input', debouncedSearch);
  if (ingredientInput) ingredientInput.addEventListener('input', debouncedSearch);
}
function debounce(fn, delay) {
  let timeout;
  return (...args) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => fn(...args), delay);
  };
}
async function handleSearch() {
  if (!searchResults || !defaultContainer) return;
  const query = searchInput?.value?.trim() || '';
  const cuisine = cuisineInput?.value?.trim() || '';
  const ingredient = ingredientInput?.value?.trim() || '';
  if (!query && !cuisine && !ingredient) {
    searchResults.style.display = 'none';
    defaultContainer.style.display = 'grid';
    return;
  }
  const params = new URLSearchParams({
    action: 'search_recipes',
    q: query,
    cuisine,
    ingredient
  });
  searchResults.innerHTML = '<p>Loading...</p>';
  searchResults.style.display = 'grid';
  defaultContainer.style.display = 'none';
  try {
    const response = await fetch(`${my_recipes_ajax.ajax_url}?${params.toString()}`);
    const html = await response.text();
    searchResults.innerHTML = html || '<p>No recipes found.</p>';
  } catch (error) {
    console.error('Recipe search failed:', error);
    searchResults.innerHTML = '<p>Failed to fetch recipes. Please try again.</p>';
  }
}
/******/ })()
;
//# sourceMappingURL=view.js.map