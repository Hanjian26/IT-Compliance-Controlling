document.querySelectorAll('.dropdown-toggle').forEach(button => {
  button.addEventListener('click', () => {
    const content = button.nextElementSibling;
    if (content) {
      content.style.display = content.style.display === 'flex' ? 'none' : 'flex';
    }
  });
});
