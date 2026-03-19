<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Kindo Mall</title>
    <!-- Favicon Dinámico con una K roja -->
    <link id="favicon" rel="icon" type="image/png" href="">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --kindo-red: #e11d48; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #ffffff;
            -webkit-tap-highlight-color: transparent;
            overflow-x: hidden;
        }
        .kindo-bg-red { background-color: var(--kindo-red); }
        .kindo-text-red { color: var(--kindo-red); }
        
        /* Estilo de pestañas */
        .tab-btn {
            position: relative;
            transition: all 0.2s ease;
        }
        .tab-active {
            color: var(--kindo-red);
            font-weight: 800;
        }
        .tab-active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 25%;
            width: 50%;
            height: 3px;
            background: var(--kindo-red);
            border-radius: 10px;
        }

        /* Animaciones */
        .fade-in { animation: fadeIn 0.3s ease-out forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        .loading-spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid var(--kindo-red);
            border-radius: 50%;
            width: 28px;
            height: 28px;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(4px);
            z-index: 100;
            display: none;
            align-items: center;
            justify-content: center;
        }
        .modal-active { display: flex !important; }
    </style>
</head>
<body class="flex flex-col min-h-screen">

    <!-- Header Fijo -->
    <header class="bg-white/95 backdrop-blur-md sticky top-0 z-40 border-b border-gray-100">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 kindo-bg-red rounded-lg flex items-center justify-center shadow-lg shadow-rose-200">
                    <span class="text-white font-black text-xl">K</span>
                </div>
                <h1 class="text-xl font-black text-gray-900 tracking-tighter">KINDO MALL</h1>
            </div>
            <div class="flex items-center space-x-3">
                <div id="conn-dot" class="w-2 h-2 rounded-full bg-gray-300 transition-colors duration-500"></div>
                <button onclick="openAdmin()" class="text-gray-400 hover:text-rose-600 transition-colors">
                    <i class="fa-solid fa-user-shield text-lg"></i>
                </button>
            </div>
        </div>
        
        <div class="flex bg-white border-b border-gray-50">
            <button onclick="switchFloor(1)" id="t1" class="tab-btn flex-1 py-3 text-xs font-black uppercase tracking-widest tab-active">Piso 1</button>
            <button onclick="switchFloor(2)" id="t2" class="tab-btn flex-1 py-3 text-xs font-black uppercase tracking-widest text-gray-400">Piso 2</button>
        </div>
    </header>

    <!-- Buscador -->
    <div class="px-4 pt-4 sticky top-[97px] z-30 bg-white pb-2">
        <div class="relative max-w-2xl mx-auto">
            <input type="text" id="search" oninput="render()" placeholder="¿Qué buscas hoy?" class="w-full pl-10 pr-4 py-3 bg-gray-50 border-0 rounded-2xl focus:ring-2 focus:ring-rose-500 outline-none text-sm transition-all shadow-inner">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-xs"></i>
        </div>
    </div>

    <!-- Contenido Principal -->
    <main class="container mx-auto px-4 py-4 flex-grow">
        <div id="grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
            <!-- Cargador con tiempo de espera reducido para ngrok -->
            <div id="loader" class="col-span-full py-24 flex flex-col items-center justify-center">
                <div class="loading-spinner mb-4"></div>
                <p class="text-[10px] text-gray-400 font-bold tracking-[0.2em] uppercase italic">Sincronizando...</p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-50 py-12 border-t border-gray-100 mb-20 md:mb-0">
        <div class="text-center px-4">
            <div class="inline-block bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6">
                <p class="text-[10px] font-black text-rose-600 uppercase tracking-tighter italic">Participa en la rifa</p>
                <p class="text-xs text-gray-500">Cada <span class="font-bold text-gray-800">RD$1,000</span> de compra es un boleto.</p>
            </div>
            <p class="text-[10px] text-gray-300 font-bold uppercase tracking-widest">Kindo Mall &copy; 2024</p>
        </div>
    </footer>

    <!-- Botón WhatsApp -->
    <a href="https://wa.me/18292634234" target="_blank" class="fixed bottom-6 right-6 bg-green-500 text-white w-14 h-14 rounded-full flex items-center justify-center shadow-xl z-50 active:scale-90 transition-transform">
        <i class="fa-brands fa-whatsapp text-3xl"></i>
    </a>

    <!-- Modales -->
    <div id="m-login" class="modal-overlay px-4" onclick="if(event.target==this) closeModals()">
        <div class="bg-white w-full max-w-sm rounded-3xl p-8 shadow-2xl">
            <h2 class="text-center font-black text-xl mb-6">ADMINISTRACIÓN</h2>
            <div class="space-y-4">
                <input type="text" id="u-in" placeholder="Usuario" class="w-full p-4 bg-gray-50 rounded-xl outline-none focus:ring-2 focus:ring-rose-500">
                <input type="password" id="p-in" placeholder="Clave" class="w-full p-4 bg-gray-50 rounded-xl outline-none focus:ring-2 focus:ring-rose-500">
                <button onclick="login()" class="w-full kindo-bg-red text-white py-4 rounded-xl font-black uppercase tracking-widest shadow-lg active:scale-[0.98] transition-transform">Entrar</button>
            </div>
        </div>
    </div>

    <div id="m-admin" class="modal-overlay px-4">
        <div class="bg-white w-full max-w-2xl h-[85vh] rounded-3xl flex flex-col overflow-hidden">
            <div class="p-5 border-b flex justify-between items-center">
                <span class="font-black text-rose-600">EDITOR KINDO</span>
                <button onclick="closeModals()" class="text-gray-300"><i class="fa-solid fa-times text-xl"></i></button>
            </div>
            <div class="flex-grow overflow-y-auto p-5 space-y-6 bg-gray-50">
                <form id="prod-form" class="space-y-3 bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                    <input type="hidden" id="f-id">
                    <input type="text" id="f-name" placeholder="Nombre del producto" required class="w-full p-3 bg-gray-50 rounded-lg border-0">
                    <div class="grid grid-cols-2 gap-3">
                        <input type="number" id="f-price" placeholder="Precio RD$" required class="w-full p-3 bg-gray-50 rounded-lg border-0">
                        <select id="f-floor" class="w-full p-3 bg-gray-50 rounded-lg border-0">
                            <option value="1">Piso 1</option>
                            <option value="2">Piso 2</option>
                        </select>
                    </div>
                    <input type="text" id="f-img" placeholder="URL de la imagen" required class="w-full p-3 bg-gray-50 rounded-lg border-0">
                    <button type="submit" id="f-btn" class="w-full kindo-bg-red text-white py-3 rounded-lg font-black uppercase tracking-widest">Guardar Producto</button>
                </form>
                <div id="admin-list" class="space-y-2"></div>
                <button onclick="logout()" class="w-full py-4 text-gray-400 text-[10px] font-bold uppercase tracking-widest">Cerrar Sesión</button>
            </div>
        </div>
    </div>

    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-app.js";
        import { getAuth, signInAnonymously, onAuthStateChanged, signInWithCustomToken } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-auth.js";
        import { getFirestore, collection, doc, setDoc, deleteDoc, onSnapshot, query, serverTimestamp } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-firestore.js";

        // Icono de carga inmediata
        const generateFavicon = () => {
            const canvas = document.createElement('canvas');
            canvas.width = 64; canvas.height = 64;
            const ctx = canvas.getContext('2d');
            ctx.fillStyle = '#e11d48';
            ctx.beginPath(); ctx.roundRect(0, 0, 64, 64, 15); ctx.fill();
            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 45px Arial Black';
            ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
            ctx.fillText('K', 32, 35);
            document.getElementById('favicon').href = canvas.toDataURL('image/png');
        };
        generateFavicon();

        const firebaseConfig = JSON.parse(__firebase_config);
        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);
        const db = getFirestore(app);
        const appId = typeof __app_id !== 'undefined' ? __app_id : 'kindo-mall-pro';

        let products = [];
        let floor = 1;
        let isFirstLoad = true;

        // FUNCIÓN CLAVE: Fuerza la desaparición del cargador si ngrok tarda
        const forceUIRender = () => {
            if (isFirstLoad) {
                isFirstLoad = false;
                const loader = document.getElementById('loader');
                if (loader) loader.style.display = 'none';
                render();
            }
        };

        // Reducido a 600ms para mejorar la respuesta en túneles ngrok
        setTimeout(forceUIRender, 600);

        const init = async () => {
            try {
                if (typeof __initial_auth_token !== 'undefined' && __initial_auth_token) {
                    await signInWithCustomToken(auth, __initial_auth_token);
                } else {
                    await signInAnonymously(auth);
                }
            } catch (e) {
                console.warn("Auth delay", e);
                forceUIRender();
            }
        };

        onAuthStateChanged(auth, (u) => {
            if (u) {
                document.getElementById('conn-dot').style.backgroundColor = '#22c55e';
                const q = collection(db, 'artifacts', appId, 'public', 'data', 'products');
                
                onSnapshot(q, (snap) => {
                    products = snap.docs.map(d => ({ id: d.id, ...d.data() }));
                    forceUIRender();
                    if(document.getElementById('m-admin').classList.contains('modal-active')) renderAdmin();
                }, (err) => {
                    console.error("Firestore Error:", err);
                    forceUIRender();
                });
            }
        });

        window.render = () => {
            const grid = document.getElementById('grid');
            if (!grid) return;
            
            const search = document.getElementById('search').value.toLowerCase();
            const filtered = products.filter(p => p.floor == floor && (p.name || '').toLowerCase().includes(search));

            if(filtered.length === 0 && !isFirstLoad) {
                grid.innerHTML = `
                    <div class="col-span-full py-20 text-center fade-in">
                        <i class="fa-solid fa-box-open text-gray-100 text-6xl mb-4"></i>
                        <p class="text-gray-300 text-xs font-bold uppercase tracking-widest">Sin productos en este piso</p>
                    </div>
                `;
                return;
            }

            grid.innerHTML = filtered.map(p => `
                <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden flex flex-col fade-in shadow-sm hover:shadow-md transition-shadow">
                    <div class="relative pt-[100%] bg-gray-50 overflow-hidden">
                        <img src="${p.img}" class="absolute inset-0 w-full h-full object-cover transition-transform hover:scale-110 duration-500" loading="lazy" onerror="this.src='https://via.placeholder.com/300x300/f8fafc/e11d48?text=KINDO'">
                    </div>
                    <div class="p-3 flex flex-col flex-grow">
                        <h3 class="font-bold text-[10px] text-gray-800 uppercase line-clamp-2 min-h-[28px] leading-tight">${p.name}</h3>
                        <p class="text-rose-600 font-black text-sm my-2 italic">RD$ ${p.price.toLocaleString()}</p>
                        <a href="https://wa.me/18292634234?text=${encodeURIComponent('Hola Kindo! Me interesa: ' + p.name)}" target="_blank" class="block w-full text-center py-2.5 bg-gray-900 text-white text-[9px] font-black uppercase tracking-widest rounded-xl active:scale-95 transition-transform">Lo quiero</a>
                    </div>
                </div>
            `).join('');
        };

        window.switchFloor = (f) => {
            floor = f;
            document.getElementById('t1').className = f === 1 ? 'tab-btn flex-1 py-3 text-xs font-black uppercase tracking-widest tab-active' : 'tab-btn flex-1 py-3 text-xs font-black uppercase tracking-widest text-gray-400';
            document.getElementById('t2').className = f === 2 ? 'tab-btn flex-1 py-3 text-xs font-black uppercase tracking-widest tab-active' : 'tab-btn flex-1 py-3 text-xs font-black uppercase tracking-widest text-gray-400';
            render();
        };

        window.openAdmin = () => {
            if(localStorage.getItem('kindo_adm_v2') === 'true') {
                document.getElementById('m-admin').classList.add('modal-active');
                renderAdmin();
            } else {
                document.getElementById('m-login').classList.add('modal-active');
            }
        };

        window.closeModals = () => {
            document.querySelectorAll('.modal-overlay').forEach(m => m.classList.remove('modal-active'));
        };

        window.login = () => {
            const u = document.getElementById('u-in').value;
            const p = document.getElementById('p-in').value;
            if(u === 'admin' && p === 'admin12') {
                localStorage.setItem('kindo_adm_v2', 'true');
                closeModals();
                openAdmin();
            } else { alert('Credenciales incorrectas'); }
        };

        window.logout = () => { localStorage.removeItem('kindo_adm_v2'); closeModals(); };

        window.renderAdmin = () => {
            const list = document.getElementById('admin-list');
            list.innerHTML = products.sort((a,b) => (b.updatedAt?.seconds || 0) - (a.updatedAt?.seconds || 0)).map(p => `
                <div class="flex items-center justify-between bg-white p-3 rounded-xl border border-gray-100 shadow-sm">
                    <div class="flex items-center space-x-3 truncate">
                        <img src="${p.img}" class="w-10 h-10 rounded-lg object-cover flex-shrink-0 bg-gray-50">
                        <div class="truncate">
                            <p class="font-bold text-[10px] uppercase truncate text-gray-700">${p.name}</p>
                            <p class="text-[9px] text-gray-400 font-bold">PISO ${p.floor} • RD$ ${p.price}</p>
                        </div>
                    </div>
                    <div class="flex space-x-1 pl-2">
                        <button onclick="editP('${p.id}')" class="w-8 h-8 flex items-center justify-center text-blue-500 hover:bg-blue-50 rounded-lg transition-colors"><i class="fa-solid fa-pen-to-square"></i></button>
                        <button onclick="delP('${p.id}')" class="w-8 h-8 flex items-center justify-center text-red-400 hover:bg-red-50 rounded-lg transition-colors"><i class="fa-solid fa-trash"></i></button>
                    </div>
                </div>
            `).join('');
        };

        window.delP = async (id) => { 
            if(confirm('¿Borrar producto?')) {
                await deleteDoc(doc(db, 'artifacts', appId, 'public', 'data', 'products', id));
            }
        };

        window.editP = (id) => {
            const p = products.find(x => x.id === id);
            if(!p) return;
            document.getElementById('f-id').value = p.id;
            document.getElementById('f-name').value = p.name;
            document.getElementById('f-price').value = p.price;
            document.getElementById('f-floor').value = p.floor;
            document.getElementById('f-img').value = p.img;
            document.getElementById('prod-form').scrollIntoView({ behavior: 'smooth' });
        };

        document.getElementById('prod-form').onsubmit = async (e) => {
            e.preventDefault();
            const btn = document.getElementById('f-btn');
            btn.disabled = true; 
            btn.innerHTML = '<i class="fa-solid fa-circle-notch animate-spin mr-2"></i> Guardando...';
            
            try {
                const id = document.getElementById('f-id').value || Date.now().toString();
                const data = {
                    name: document.getElementById('f-name').value.trim(),
                    price: Number(document.getElementById('f-price').value),
                    floor: Number(document.getElementById('f-floor').value),
                    img: document.getElementById('f-img').value.trim(),
                    updatedAt: serverTimestamp()
                };

                await setDoc(doc(db, 'artifacts', appId, 'public', 'data', 'products', id), data);
                document.getElementById('prod-form').reset();
                document.getElementById('f-id').value = '';
            } catch (err) {
                alert("Error: Revisa la conexión o permisos.");
            } finally {
                btn.disabled = false; 
                btn.innerText = 'Guardar Producto';
            }
        };

        init();
    </script>
</body>
</html>
<!-- Elfsight AI Chatbot | Untitled AI Chatbot -->
<script src="https://elfsightcdn.com/platform.js" async></script>
<div class="elfsight-app-11fb5588-0a4f-4801-b063-561df9017d1f" data-elfsight-app-lazy></div>
