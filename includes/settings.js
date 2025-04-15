function showContent(contentClass) {
    document.querySelectorAll('.man-cat-content, .man-group-content, .altro-content').forEach(element => {
        element.style.display = 'none';
    });

    let content = document.querySelector('.' + contentClass);
    if (content) {
        content.style.display = 'block';
    }
}