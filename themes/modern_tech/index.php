<?php
// Load Language Data
$default_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : "en";
$lang = $default_lang;
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    $_SESSION['lang'] = $lang;
}

require_once dirname(dirname(dirname(__FILE__))) . "/lang_converter/converter.php";
$jasonFilePath = dirname(dirname(dirname(__FILE__))).'/lang-json/' . $lang . '/index_en.json';

function readContents($file_path) {
    if (file_exists($file_path)) {
        return file_get_contents($file_path);
    } else {
        return '{}';
    }
}

$PAGE_DATA = json_decode(readContents($jasonFilePath), true);
$arrayData = langConverter($lang, 'nav'); 
$imgPath = $publicAccessUrl . 'themes/modern_tech/img/';
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $PAGE_DATA['title'] ?? 'Workmate'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <script>
        const publicAccessUrl = `<?= $publicAccessUrl ?>`;
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            200: '#ddd6fe',
                            300: '#c4b5fd',
                            400: '#a78bfa',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            800: '#5b21b6',
                            900: '#4c1d95',
                        },
                        secondary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        dark: {
                            900: '#0f172a',
                            800: '#1e293b',
                            700: '#334155',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .glass-panel {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .gradient-text {
            background: linear-gradient(135deg, #c4b5fd 0%, #7c3aed 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero-gradient {
            background: radial-gradient(circle at top right, #4c1d95 0%, #0f172a 40%, #0f172a 100%);
        }
    </style>
</head>
<body class="bg-dark-900 text-slate-300 antialiased selection:bg-primary-500 selection:text-white">

    <!-- Navigation -->
    <nav class="fixed w-full z-50 top-0 transition-all duration-300 bg-dark-900/80 backdrop-blur-md border-b border-white/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex-shrink-0 flex items-center gap-3">
                    <img class="h-8 w-auto" src="<?= $publicAccessUrl . 'themes/modern_tech/img/core-img/logo.png'; ?>" onerror="this.style.display='none'" alt="Workmate">
                    <span class="font-bold text-xl text-white tracking-tight">Workmate</span>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#about" class="text-sm font-medium hover:text-white transition-colors">About</a>
                    <a href="#features" class="text-sm font-medium hover:text-white transition-colors">Features</a>
                    <a href="#services" class="text-sm font-medium hover:text-white transition-colors">Services</a>
                    <a href="#contact" class="text-sm font-medium hover:text-white transition-colors">Contact</a>
                    <a href="#login_section" class="px-5 py-2.5 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-500 rounded-full transition-all shadow-lg shadow-primary-900/20">Login</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden hero-gradient">
        <!-- Background Elements -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-primary-600/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-96 h-96 bg-secondary-600/10 rounded-full blur-3xl"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <!-- Content -->
                <div class="text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/5 border border-white/10 mb-8">
                        <span class="flex h-2 w-2 rounded-full bg-primary-400"></span>
                        <span class="text-xs font-medium text-primary-200 tracking-wide uppercase">New Generation Platform</span>
                    </div>
                    
                    <?php 
                        $heroSlides = $PAGE_DATA['head-carousel'] ?? [];
                        $heroData = $heroSlides[0] ?? [
                            'title' => 'Manage Your Workflow With Intelligent Tools',
                            'sub-title' => 'Workmate',
                            'subsub-title' => 'Streamline your daily tasks, collaborate with your team, and achieve more with our all-in-one project management solution.',
                            'extra-button' => 'Get Started'
                        ];
                    ?>

                    <h1 class="text-5xl lg:text-7xl font-bold text-white tracking-tight leading-tight mb-6">
                        <?= $heroData['title']; ?>
                    </h1>
                    <p class="text-lg text-slate-400 mb-10 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                        <?= $heroData['subsub-title']; ?>
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="javascript:void(0);" onclick="toggleModal('signup_modal', true)" class="px-8 py-4 text-base font-semibold text-white bg-primary-600 hover:bg-primary-500 rounded-full transition-all shadow-lg shadow-primary-900/20 flex items-center justify-center gap-2">
                            <?= $heroData['extra-button'] ?? 'Get Started'; ?> <i class="fas fa-arrow-right text-sm"></i>
                        </a>
                        <a href="#features" class="px-8 py-4 text-base font-semibold text-white bg-white/5 hover:bg-white/10 border border-white/10 rounded-full transition-all flex items-center justify-center">
                            Learn More
                        </a>
                    </div>
                </div>

                <!-- Login Card -->
                <div id="login_section" class="w-full max-w-md mx-auto">
                    <div class="glass-panel p-8 rounded-2xl shadow-2xl relative overflow-hidden group">
                        <div class="absolute inset-0 bg-gradient-to-br from-primary-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        
                        <div class="relative z-10">
                            <div class="text-center mb-8">
                                <h3 class="text-2xl font-bold text-white mb-2">
                                    <?= str_replace('HiWorkmate', 'Workmate', $arrayData['lang_hiworkmate_login'] ?? 'Welcome Back'); ?>
                                </h3>
                                <p class="text-slate-400 text-sm">Enter your credentials to access your workspace</p>
                            </div>

                            <form class="login_form space-y-5">
                                <div>
                                    <label class="block text-xs font-medium text-slate-400 uppercase tracking-wider mb-2">Username</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500">
                                            <i class="fas fa-user"></i>
                                        </span>
                                        <input name="username" type="text" class="w-full bg-dark-800 border border-dark-700 text-white text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block pl-10 p-3 transition-colors placeholder-slate-600" placeholder="Enter your username" required>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-400 uppercase tracking-wider mb-2">Password</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                        <input name="password" type="password" class="w-full bg-dark-800 border border-dark-700 text-white text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block pl-10 p-3 transition-colors placeholder-slate-600" placeholder="••••••••" required>
                                        <span class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer text-slate-500 hover:text-white toggle_password">
                                            <i class="far fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                
                                <button type="submit" class="w-full text-white bg-primary-600 hover:bg-primary-500 focus:ring-4 focus:ring-primary-900 font-medium rounded-lg text-sm px-5 py-3 text-center transition-all shadow-lg shadow-primary-900/20">
                                    <?= $arrayData['lang_log_in'] ?? 'Log In'; ?>
                                </button>
                            </form>

                            <div class="mt-6 flex items-center justify-between text-sm">
                                <a href="javascript:void(0);" class="text-slate-400 hover:text-white transition-colors forgotten_password">
                                    <?= $arrayData['lang_forgotten_password?']; ?>
                                </a>
                                <button type="button" class="text-primary-400 hover:text-primary-300 font-medium transition-colors create_new_account_button">
                                    <?= $arrayData['lang_create_new_account']; ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Grid -->
    <section id="features" class="py-24 bg-dark-800 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-white mb-4">Powerful Features</h2>
                <p class="text-slate-400 max-w-2xl mx-auto">Everything you need to manage your team and projects effectively.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php 
                $serviceSection = $PAGE_DATA['sections']['service'] ?? [];
                if (isset($serviceSection['list']) && is_array($serviceSection['list'])) {
                    foreach ($serviceSection['list'] as $service) {
                ?>
                <div class="p-8 rounded-2xl bg-dark-900 border border-dark-700 hover:border-primary-500/50 transition-all group">
                    <div class="w-12 h-12 rounded-lg bg-primary-900/30 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <i class="<?= $service['icon'] ?? 'fas fa-check-circle'; ?> text-xl text-primary-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3"><?= $service['title'] ?? ''; ?></h3>
                    <p class="text-slate-400 leading-relaxed">
                        <?= $service['short-description'] ?? ''; ?>
                    </p>
                </div>
                <?php 
                    }
                }
                ?>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-24 bg-dark-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="relative">
                    <div class="absolute inset-0 bg-primary-600/20 blur-3xl rounded-full"></div>
                    <img src="<?= $imgPath ?>bg-img/about1.jpg" onerror="this.src='https://placehold.co/600x400?text=About+Us'" alt="About" class="relative z-10 rounded-2xl shadow-2xl border border-white/10">
                </div>
                <div>
                    <?php $aboutSection = $PAGE_DATA['sections']['about'] ?? []; ?>
                    <h2 class="text-3xl lg:text-4xl font-bold text-white mb-6"><?= $aboutSection['title'] ?? 'About Us'; ?></h2>
                    <h3 class="text-xl text-primary-400 font-medium mb-6"><?= $aboutSection['sub-title'] ?? ''; ?></h3>
                    <div class="text-slate-400 space-y-4 leading-relaxed">
                        <?= $aboutSection['description'] ?? ''; ?>
                    </div>
                    <div class="mt-8 p-6 bg-dark-800 rounded-xl border-l-4 border-primary-500">
                        <p class="text-white italic">
                            "<?= $aboutSection['keynote'] ?? ''; ?>"
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark-900 border-t border-white/5 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-12 mb-12">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-3 mb-6">
                        <img class="h-8 w-auto" src="<?= $publicAccessUrl . 'themes/modern_tech/img/core-img/logo.png'; ?>" onerror="this.style.display='none'" alt="Workmate">
                        <span class="font-bold text-xl text-white">Workmate</span>
                    </div>
                    <p class="text-slate-400 max-w-sm">
                        Empowering teams to achieve more with intelligent project management tools.
                    </p>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-6">Quick Links</h4>
                    <ul class="space-y-3">
                        <li><a href="#about" class="text-slate-400 hover:text-primary-400 transition-colors">About Us</a></li>
                        <li><a href="#features" class="text-slate-400 hover:text-primary-400 transition-colors">Features</a></li>
                        <li><a href="#services" class="text-slate-400 hover:text-primary-400 transition-colors">Services</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-6">Connect</h4>
                    <div class="flex gap-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-dark-800 flex items-center justify-center text-slate-400 hover:bg-primary-600 hover:text-white transition-all">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-dark-800 flex items-center justify-center text-slate-400 hover:bg-primary-600 hover:text-white transition-all">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-dark-800 flex items-center justify-center text-slate-400 hover:bg-primary-600 hover:text-white transition-all">
                            <i class="fab fa-github"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="border-t border-white/5 pt-8 text-center text-slate-500 text-sm">
                &copy; <?= date('Y'); ?> Workmate. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <script src='//www.google.com/recaptcha/api.js?render=6Le-0EQpAAAAAHQlefT-hdZhSf7oWvLw77aAd_ZA'></script>
    <script>
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-bottom-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    </script>
    <script src="<?= $publicAccessUrl ?>themes/modern_tech/js/login.js"></script>

    <?php require_once "themes/modern_tech/signup_modal.php"; ?>

</body>
</html>
