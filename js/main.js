marked.setOptions({
    highlight: function(code, lang) {
        if (Prism.languages[lang]) {
            return Prism.highlight(code, Prism.languages[lang], lang);
        }
        return code;
    }
});

function generateTOC(content) {
    const headings = content.match(/#{2,3}\s+.+/g) || [];
    if (headings.length === 0) return '';

    const toc = ['<div class="toc">', '<h2>Table of Contents</h2>', '<ul>'];
    
    headings.forEach(heading => {
        const level = heading.match(/#{2,3}/)[0].length;
        const text = heading.replace(/#{2,3}\s+/, '');
        const slug = text.toLowerCase().replace(/[^\w]+/g, '-');
        const indent = level === 2 ? '' : 'style="margin-left: 1rem;"';
        toc.push(`<li ${indent}><a href="#${slug}">${text}</a></li>`);
    });

    toc.push('</ul>', '</div>');
    return toc.join('\n');
}

function processMarkdown(content) {
    content = content.replace(/^(#{2,3})\s+(.+)$/gm, (match, hashes, title) => {
        const slug = title.toLowerCase().replace(/[^\w]+/g, '-');
        return `${hashes} ${title} {#${slug}}`;
    });

    const toc = generateTOC(content);

    const htmlContent = marked(content);

    return toc + htmlContent;
}

let posts = [];

async function loadPosts() {
    try {
        const response = await fetch('posts/posts.json');
        const data = await response.json();
        posts = data.posts || [];
        
        const postsContainer = document.getElementById('postsContainer');
        if (postsContainer) {
            postsContainer.innerHTML = posts.map(post => `
                <a href="post.php?slug=${post.slug}" class="post-card">
                    <h3>${post.title}</h3>
                    <p>${post.excerpt}</p>
                    <div class="post-date">${new Date(post.date).toLocaleDateString()}</div>
                </a>
            `).join('');
        }
    } catch (error) {
        console.error('Error loading posts:', error);
    }
}

function searchPosts(query) {
    if (!query.trim()) return [];
    
    const searchTerm = query.toLowerCase().trim();
    return posts.filter(post => {
        return post.tags.some(tag => tag.toLowerCase().includes(searchTerm));
    });
}

function displaySearchResults(results) {
    const searchResults = document.getElementById('searchResults');
    if (!results.length) {
        searchResults.innerHTML = '<div class="search-result-item">No results found</div>';
        return;
    }

    searchResults.innerHTML = results.map(post => `
        <a href="post.php?slug=${post.slug}" class="search-result-item">
            <h3>${post.title}</h3>
            <div class="excerpt">${post.excerpt}</div>
            <div class="tags">
                ${post.tags.map(tag => `<span class="tag">${tag}</span>`).join('')}
            </div>
            <div class="date">${new Date(post.date).toLocaleDateString()}</div>
        </a>
    `).join('');
}

function toggleSearch() {
    const overlay = document.getElementById('searchOverlay');
    const searchInput = document.getElementById('searchInput');
    
    if (overlay.style.display === 'flex') {
        overlay.style.display = 'none';
        searchInput.value = '';
    } else {
        overlay.style.display = 'flex';
        searchInput.focus();
    }
}

function performSearch() {
    const searchInput = document.getElementById('searchInput');
    const results = searchPosts(searchInput.value);
    displaySearchResults(results);
}

document.addEventListener('click', (e) => {
    const overlay = document.getElementById('searchOverlay');
    const searchContainer = document.querySelector('.search-container');
    
    if (overlay.style.display === 'flex' && 
        !searchContainer.contains(e.target) && 
        !e.target.closest('a[onclick="toggleSearch()"]')) {
        overlay.style.display = 'none';
        document.getElementById('searchInput').value = '';
    }
});

document.addEventListener('keydown', (e) => {
    const overlay = document.getElementById('searchOverlay');
    if (e.key === 'Escape') {
        overlay.style.display = 'none';
    } else if (e.key === 'k' && e.ctrlKey) {
        e.preventDefault();
        toggleSearch();
    } else if (e.key === 'Enter' && overlay.style.display === 'flex') {
        e.preventDefault();
        performSearch();
    }
});

document.addEventListener('DOMContentLoaded', () => {
    loadPosts();
    
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch();
            }
        });

        searchInput.addEventListener('input', (e) => {
            performSearch();
        });
    }

    const postContent = document.querySelector('.post-content');
    if (postContent && postContent.hasAttribute('data-markdown')) {
        const markdown = postContent.textContent;
        postContent.innerHTML = processMarkdown(markdown);
    }
}); 