<?php
$basePath = dirname(dirname(dirname(__FILE__)));
include_once($basePath . "/configmanager/org_configuration.php");
if (!defined("DB_USER")) {
	include_once $basePath . '/php/db/config.php';
}

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

// Load UI Language Data
if (!isset($arrayData)) {
    $arrayData = array();
}
$arrayData = array_merge($arrayData, langConverter($lang, 'index'));
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $PAGE_DATA['title'] ?? 'Workmate'; ?></title>
    <meta name="description" content="<?= $PAGE_DATA['sections']['about']['description'] ?? ''; ?>">
    <link rel="icon" href="<?= $publicAccessUrl . $response['orglogourl']; ?>">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@400;500;700;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: {
                            900: '#0B0C10',
                            800: '#1F2833',
                            700: '#2C3531',
                        },
                        neon: {
                            cyan: '#66FCF1',
                            blue: '#45A29E',
                        },
                        text: {
                            main: '#C5C6C7',
                            muted: '#8892b0',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Orbitron', 'sans-serif'],
                    },
                    backgroundImage: {
                        'hero-pattern': "linear-gradient(to right bottom, rgba(11, 12, 16, 0.9), rgba(31, 40, 51, 0.9)), url('<?= $publicAccessUrl ?>/themes/va/images/bg_1.jpg')",
                    }
                }
            }
        }
    </script>
    <style>
        .glass {
            background: rgba(31, 40, 51, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(102, 252, 241, 0.1);
        }
        .text-glow {
            text-shadow: 0 0 10px rgba(102, 252, 241, 0.5);
        }
        .box-glow:hover {
            box-shadow: 0 0 20px rgba(102, 252, 241, 0.2);
        }
    </style>
</head>
<body class="bg-dark-900 text-text-main font-sans antialiased selection:bg-neon-cyan selection:text-dark-900">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 glass transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex-shrink-0 flex items-center gap-3">
                    <img class="h-10 w-auto" src="<?= $publicAccessUrl . $response['orglogourl']; ?>" alt="Logo">
                    <span class="font-heading font-bold text-2xl text-neon-cyan tracking-wider">WORKMATE</span>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-8">
                        <a href="#" class="text-neon-cyan px-3 py-2 rounded-md text-sm font-medium font-heading">Home</a>
                        <a href="#about" class="hover:text-neon-cyan transition-colors px-3 py-2 rounded-md text-sm font-medium">About</a>
                        <a href="#services" class="hover:text-neon-cyan transition-colors px-3 py-2 rounded-md text-sm font-medium">Services</a>
                        <a href="#contact" class="hover:text-neon-cyan transition-colors px-3 py-2 rounded-md text-sm font-medium">Contact</a>
                        <a href="#login_section" class="border border-neon-cyan text-neon-cyan hover:bg-neon-cyan hover:text-dark-900 px-4 py-2 rounded-md text-sm font-medium transition-all duration-300 font-heading">LOGIN</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <?php 
        $heroSlides = $PAGE_DATA['head-carousel'] ?? [];
        $heroData = $heroSlides[0] ?? [
            'title' => 'Future of Task Management',
            'subsub-title' => 'Streamline your workflow with AI-powered efficiency.',
        ];
    ?>
    <div class="relative min-h-screen flex items-center justify-center bg-hero-pattern bg-cover bg-center bg-no-repeat bg-fixed pt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Hero Text -->
                <div class="text-center lg:text-left">
                    <h1 class="text-5xl md:text-7xl font-heading font-black text-white mb-6 tracking-tight leading-tight">
                        <span class="block text-transparent bg-clip-text bg-gradient-to-r from-neon-cyan to-neon-blue animate-pulse">
                            <?= $heroData['title']; ?>
                        </span>
                    </h1>
                    <p class="mt-4 text-xl md:text-2xl text-text-muted max-w-2xl mx-auto lg:mx-0 mb-10">
                        <?= $heroData['subsub-title']; ?>
                    </p>
                    <div class="flex justify-center lg:justify-start gap-4">
                        <a href="#about" class="group relative px-8 py-4 bg-transparent overflow-hidden rounded-none border-2 border-neon-cyan text-neon-cyan font-heading font-bold tracking-widest hover:text-dark-900 transition-colors duration-300">
                            <span class="absolute w-0 h-0 transition-all duration-500 ease-out bg-neon-cyan rounded-full group-hover:w-56 group-hover:h-56 -ml-2 -mt-2 opacity-100"></span>
                            <span class="relative">LEARN MORE</span>
                        </a>
                    </div>
                </div>

                <!-- Login Form -->
                <div id="login_section" class="w-full max-w-md mx-auto lg:ml-auto">
                    <div class="glass p-8 rounded-xl box-glow">
                        <div class="text-2xl font-heading font-bold text-white mb-6 text-center">
                            <?= str_replace('HiWorkmate', 'Workmate', $arrayData['lang_hiworkmate_login'] ?? 'Workmate Login'); ?>
                        </div>
                        <form class="login_form space-y-4">
                            <div>
                                <input name="username" type="text" class="w-full bg-dark-800 border border-dark-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-cyan focus:ring-1 focus:ring-neon-cyan transition-colors" minlength="3" autocomplete="off" placeholder="Enter Your Username" required>
                            </div>
                            <div>
                                <input name="password" type="password" class="w-full bg-dark-800 border border-dark-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-cyan focus:ring-1 focus:ring-neon-cyan transition-colors" minlength="6" autocomplete="off" placeholder="Enter Your Password" required>
                            </div>
                            <button type="submit" class="w-full bg-neon-cyan text-dark-900 font-heading font-bold py-3 rounded-lg hover:bg-neon-blue transition-colors duration-300 uppercase tracking-widest shadow-lg shadow-neon-cyan/20">
                                <?= $arrayData['lang_log_in'] ?? 'Login'; ?>
                            </button>
                        </form>
                        
                        <div class="text-center mt-4">
                            <a href="javascript:void(0);" class="forgotten_password text-sm text-text-muted hover:text-neon-cyan transition-colors">
                                <?= $arrayData['lang_forgotten_password?'] ?? 'Forgot Password?'; ?>
                            </a>
                        </div>
                        
                        <div class="relative my-6">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-dark-700"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-dark-800 text-text-muted">Or</span>
                            </div>
                        </div>
                        
                        <button type="button" class="create_new_account_button w-full bg-transparent border border-neon-blue text-neon-blue font-heading font-bold py-3 rounded-lg hover:bg-neon-blue hover:text-white transition-colors duration-300 uppercase tracking-widest">
                            <?= $arrayData['lang_create_new_account'] ?? 'Create New Account'; ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Scroll Indicator -->
        <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 animate-bounce hidden lg:block">
            <svg class="w-6 h-6 text-neon-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
            </svg>
        </div>
    </div>

    <!-- About Section -->
    <?php $aboutData = $PAGE_DATA['sections']['about'] ?? []; ?>
    <section id="about" class="py-24 bg-dark-900 relative overflow-hidden">
        <!-- Decorative Elements -->
        <div class="absolute top-0 left-0 w-64 h-64 bg-neon-cyan opacity-5 rounded-full filter blur-3xl transform -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-neon-blue opacity-5 rounded-full filter blur-3xl transform translate-x-1/2 translate-y-1/2"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16">
                <h2 class="text-neon-cyan font-heading text-sm tracking-[0.2em] uppercase mb-2"><?= $aboutData['sub-title'] ?? 'ABOUT US'; ?></h2>
                <h3 class="text-4xl md:text-5xl font-heading font-bold text-white"><?= $aboutData['title'] ?? 'Why Choose Workmate?'; ?></h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="relative group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-neon-cyan to-neon-blue rounded-lg blur opacity-25 group-hover:opacity-75 transition duration-1000 group-hover:duration-200"></div>
                    <div class="relative rounded-lg overflow-hidden border border-dark-700">
                        <img src="<?= $publicAccessUrl ?>/themes/va/images/about.jpg" alt="About Us" class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500">
                    </div>
                </div>
                
                <div class="space-y-8">
                    <div class="glass p-8 rounded-xl box-glow transition-all duration-300">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="p-3 bg-dark-800 rounded-lg border border-neon-cyan/20">
                                <svg class="w-6 h-6 text-neon-cyan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <h4 class="text-xl font-heading font-bold text-white"><?= $aboutData['keynote'] ?? 'Boost Productivity'; ?></h4>
                        </div>
                        <p class="text-text-muted leading-relaxed">
                            <?= $aboutData['description'] ?? 'Experience the next generation of task management. Our AI-driven platform adapts to your workflow, ensuring you stay ahead of the curve.'; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <?php $servicesData = $PAGE_DATA['sections']['service'] ?? []; ?>
    <section id="services" class="py-24 bg-dark-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-neon-cyan font-heading text-sm tracking-[0.2em] uppercase mb-2"><?= $servicesData['sub-title'] ?? 'SERVICES'; ?></h2>
                <h3 class="text-4xl md:text-5xl font-heading font-bold text-white mb-6"><?= $servicesData['title'] ?? 'Our Capabilities'; ?></h3>
                <p class="text-text-muted max-w-2xl mx-auto"><?= $servicesData['description'] ?? ''; ?></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php 
                $servicesList = $servicesData['list'] ?? [];
                foreach ($servicesList as $service): 
                ?>
                <div class="glass p-8 rounded-xl group hover:-translate-y-2 transition-all duration-300 border border-dark-700 hover:border-neon-cyan/50">
                    <div class="w-14 h-14 bg-dark-900 rounded-lg flex items-center justify-center mb-6 group-hover:bg-neon-cyan/10 transition-colors">
                        <i class="<?= $service['icon'] ?? 'fa fa-check'; ?> text-2xl text-neon-cyan"></i>
                    </div>
                    <h4 class="text-xl font-heading font-bold text-white mb-3 group-hover:text-neon-cyan transition-colors"><?= $service['title']; ?></h4>
                    <p class="text-text-muted text-sm leading-relaxed"><?= $service['short-description']; ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-24 bg-dark-900 relative">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="glass p-10 rounded-2xl border border-dark-700">
                <div class="text-center mb-10">
                    <h3 class="text-3xl font-heading font-bold text-white mb-4">Ready to Upgrade?</h3>
                    <p class="text-text-muted">Join the future of work today.</p>
                </div>
                
                <form action="#" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-text-muted mb-2">Name</label>
                            <input type="text" class="w-full bg-dark-800 border border-dark-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-cyan focus:ring-1 focus:ring-neon-cyan transition-colors" placeholder="John Doe">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-text-muted mb-2">Email</label>
                            <input type="email" class="w-full bg-dark-800 border border-dark-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-cyan focus:ring-1 focus:ring-neon-cyan transition-colors" placeholder="john@example.com">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-muted mb-2">Message</label>
                        <textarea rows="4" class="w-full bg-dark-800 border border-dark-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-cyan focus:ring-1 focus:ring-neon-cyan transition-colors" placeholder="Tell us about your needs..."></textarea>
                    </div>
                    <button type="submit" class="w-full bg-neon-cyan text-dark-900 font-heading font-bold py-4 rounded-lg hover:bg-neon-blue transition-colors duration-300 uppercase tracking-widest">
                        Send Message
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark-900 border-t border-dark-800 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-2">
                    <img class="h-8 w-auto" src="<?= $publicAccessUrl . $response['orglogourl']; ?>" alt="Logo">
                    <span class="font-heading font-bold text-xl text-white">WORKMATE</span>
                </div>
                <div class="text-text-muted text-sm">
                    &copy; <?= date('Y'); ?> Workmate. All rights reserved.
                </div>
                <div class="flex gap-6">
                    <a href="#" class="text-text-muted hover:text-neon-cyan transition-colors"><i class="fa fa-twitter text-xl"></i></a>
                    <a href="#" class="text-text-muted hover:text-neon-cyan transition-colors"><i class="fa fa-facebook text-xl"></i></a>
                    <a href="#" class="text-text-muted hover:text-neon-cyan transition-colors"><i class="fa fa-instagram text-xl"></i></a>
                </div>
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
    <script src="<?= $publicAccessUrl ?>themes/tailwind/js/login.js"></script>

    <?php require_once "themes/tailwind/signup_modal.php"; ?>

</body>
</html>
