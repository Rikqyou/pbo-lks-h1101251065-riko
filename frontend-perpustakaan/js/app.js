function handleRoute() {
    const hash = window.location.hash || '#dashboard';
    
    if (hash === '#dashboard') {
        DashboardPage.render();
    } else if (hash === '#buku') {
        BooksPage.render();
    } else if (hash === '#kategori') {
        CategoryPage.render();
    } else {
        DashboardPage.render();
    }
}

window.addEventListener('hashchange', handleRoute);
window.addEventListener('DOMContentLoaded', handleRoute);
