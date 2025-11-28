<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        background: #ffffff;
        min-height: 100vh;
        color: #503e2cff;
    }

    /* --- Sidebar --- */
    .sidebar {
        width: 220px;
        background: #1a1a1a;
        color: #b75a03ff;
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        padding: 2rem 1rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        z-index: 1000;
    }
    .sidebar .logo { font-size:1.5rem; font-weight:700; color:#b75a03ff; margin-bottom:2rem; }
    .sidebar nav { display: flex; flex-direction: column; gap: 0.6rem; flex: 1; }
    .sidebar nav a {
        color:#aaa;
        text-decoration:none;
        font-weight:500;
        padding:0.6rem 1rem;
        border-radius:6px;
        transition:0.2s;
        display: block;
    }
    .sidebar nav a:hover { background:#2c2c2c; color:#fff; }
    .sidebar a.active {
        background: #ff9320ff;
        color: #fff;
    }
    .sidebar nav a.logout { color:#e74c3c !important; margin-top:auto; }

    /* --- Main content --- */
    .main-content { margin-left: 220px; padding: 2rem; }

    .page-title { 
        font-size:1.8rem; 
        margin-bottom:1.5rem; 
        font-weight:600; 
        color:#fff;
        background: linear-gradient(135deg, #b75a03ff 0%, #ff9320ff 100%);
        padding: 1rem 1.5rem;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(183, 90, 3, 0.3);
    }

    @media (max-width:768px){ .main-content { margin-left: 0; padding:1rem; } .sidebar { width: 100%; height: auto; position: relative; } }
</style>
