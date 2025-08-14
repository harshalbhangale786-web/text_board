// 1. Captcha refresh function
window.refreshCaptcha = function refreshCaptcha() {
  var img = document.getElementById('captchaImg');
  if (!img) return;
  var url = 'captcha.php?ts=' + Date.now();
  img.setAttribute('src', url);
};

// Helper: set content preview/full text based on expanded state
function updateCardContent(card){
  var box = card.querySelector('.text-card__content');
  if (!box) return;
  var full = box.getAttribute('data-full') || box.textContent || '';
  if (card.classList.contains('expanded')) {
    box.textContent = full; // show full text
    box.classList.add('expanded');
  } else {
    box.textContent = full; // CSS line-clamp will truncate visually
    box.classList.remove('expanded');
  }
}

// 2. Expand/collapse logic — no arrow, close on outside click
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.text-card').forEach(function (card) {
    let contentBox = card.querySelector('.text-card__content');

    // Open card on clicking anywhere inside it
    card.addEventListener('click', function (e) {
      if (!card.classList.contains('expanded')) {
        // Close other expanded cards
        let expanded = card.parentElement.querySelector('.text-card.expanded');
        if (expanded && expanded !== card) {
          expanded.classList.remove('expanded');
          updateCardContent(expanded);
        }
        card.classList.add('expanded');
        updateCardContent(card);
        card.scrollIntoView({behavior: 'smooth', block: 'start'});
      }
    });

    // Prevent click inside contentBox from bubbling (e.g. text select)
    if (contentBox) {
      contentBox.addEventListener('mousedown', function (e) {
        e.stopPropagation();
      });
    }

    // Initialize preview content
    updateCardContent(card);
  });

  // Close all expanded cards on clicking outside any card
  document.addEventListener('click', function (e) {
    if (!e.target.closest('.text-card')) {
      document.querySelectorAll('.text-card.expanded').forEach(function(card) {
        card.classList.remove('expanded');
        updateCardContent(card);
      });
    }
  });
});
