function showContent(contentClass) {
    // hide everything
    document.querySelectorAll('.man-cat-content, .altro-content').forEach(element => {
        element.style.display = 'none';
    });

    // shows only correct elemetn
    let content = document.querySelector('.' + contentClass);
    if (content) {
        content.style.display = 'block';
    }
}