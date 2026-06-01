(function () {
    var input = document.querySelector('[data-search]');
    if (!input) return;
    var items = Array.prototype.slice.call(document.querySelectorAll('[data-search-item]'));
    input.addEventListener('input', function () {
        var query = input.value.trim().toLowerCase();
        items.forEach(function (item) {
            var haystack = item.getAttribute('data-search-item').toLowerCase();
            item.style.display = haystack.indexOf(query) === -1 ? 'none' : '';
        });
    });
})();
