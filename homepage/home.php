<?php require_once __DIR__ . '/../config/helpers.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventify | Event Management Made Simple</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <?php echo eventify_sweetalert_assets(); ?>
    <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#7C00D8',
            secondary: '#A855F7',
            soft: '#F6F3FF',
            dark: '#111827'
          },
          boxShadow: {
            soft: '0 15px 35px rgba(124, 0, 216, 0.15)'
          }
        }
      }
    }
    </script>
    <link rel="stylesheet" href="assets/css/homepage.css">
</head>
<body class="bg-soft text-dark antialiased">
    <header class="sticky top-0 z-40 border-b border-white/70 bg-white/85 backdrop-blur">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="home.php" class="flex items-center gap-3 font-semibold tracking-tight text-dark">
                <span class="grid h-9 w-9 place-items-center rounded-2xl bg-primary text-white shadow-soft">E</span>
                <span class="text-xl">Eventify</span>
            </a>

            <button class="rounded-xl p-2 text-primary md:hidden" type="button" data-mobile-menu-button aria-label="Open menu">
                <span class="block h-0.5 w-6 bg-current"></span>
                <span class="mt-1.5 block h-0.5 w-6 bg-current"></span>
                <span class="mt-1.5 block h-0.5 w-6 bg-current"></span>
            </button>

            <div class="hidden items-center gap-3 md:flex">
                <a href="#features" class="rounded-xl px-4 py-2 text-sm font-semibold text-slate-600 hover:text-primary">Features</a>
                <a href="#packages" class="rounded-xl px-4 py-2 text-sm font-semibold text-slate-600 hover:text-primary">Packages</a>
                <button type="button" data-auth-modal-open="login" class="rounded-xl px-4 py-2 text-sm font-semibold text-primary hover:bg-purple-50">Login</button>
                <button type="button" data-auth-modal-open="register" class="rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white shadow-soft hover:bg-secondary">Sign Up</button>
            </div>
        </nav>

        <div class="hidden border-t border-purple-100 bg-white px-4 py-4 md:hidden" data-mobile-menu>
            <div class="grid gap-2">
                <a href="#features" class="rounded-xl px-4 py-3 font-semibold text-slate-700 hover:bg-purple-50">Features</a>
                <a href="#packages" class="rounded-xl px-4 py-3 font-semibold text-slate-700 hover:bg-purple-50">Packages</a>
                <button type="button" data-auth-modal-open="login" class="rounded-xl px-4 py-3 text-left font-semibold text-slate-700 hover:bg-purple-50">Login</button>
                <button type="button" data-auth-modal-open="register" class="rounded-xl bg-primary px-4 py-3 text-center font-bold text-white">Sign Up</button>
            </div>
        </div>
    </header>

    <main>
        <!-- Hero -->
        <section class="relative isolate min-h-[82vh] overflow-hidden">
            <img class="absolute inset-0 h-full w-full object-cover" src="https://images.unsplash.com/photo-1511795409834-ef04bbd61622?auto=format&fit=crop&w=1800&q=85" alt="Elegant event venue with guests">
            <div class="absolute inset-0 bg-gradient-to-br from-white via-white/90 to-purple-900/25"></div>
            <div class="relative mx-auto grid min-h-[82vh] max-w-7xl content-center px-4 py-16 sm:px-6 lg:px-8">
                <div class="max-w-3xl">
                    <span class="inline-flex rounded-full border border-purple-200 bg-white/80 px-4 py-2 text-xs font-semibold uppercase tracking-[0.25em] text-primary shadow-sm">New version is live</span>
                    <h1 class="mt-6 max-w-2xl text-5xl font-semibold leading-[1.02] tracking-tight text-dark sm:text-6xl lg:text-7xl">
                        Event Management Made Simple
                    </h1>
                    <p class="mt-6 max-w-xl text-lg leading-8 text-slate-700">
                        Streamline reservations, approvals, calendars, and event packages in one polished system built for modern organizers.
                    </p>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <button type="button" data-auth-modal-open="register" class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-primary to-secondary px-6 py-3 text-sm font-semibold text-white shadow-soft hover:scale-[1.01]">Get Started</button>
                        <a href="#packages" class="inline-flex items-center justify-center rounded-2xl border border-purple-200 bg-white/80 px-6 py-3 text-sm font-semibold text-primary hover:bg-white">View Packages</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features -->
        <section id="features" class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-primary">Engineered for Excellence</p>
                <h2 class="mt-3 text-3xl font-semibold tracking-tight text-dark sm:text-4xl">Every event detail stays organized.</h2>
                <p class="mt-4 text-slate-600">From reservation requests to final event history, Eventify keeps the workflow clean and defense-ready.</p>
            </div>

            <div class="mt-10 grid gap-5 md:grid-cols-3">
                <article class="rounded-3xl bg-white p-6 shadow-soft">
                    <div class="grid h-12 w-12 place-items-center rounded-2xl bg-purple-100 text-xl text-primary">+</div>
                    <h3 class="mt-5 text-xl font-semibold">Seamless Planning</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-600">Create reservations, select packages, and track services with clear forms and reviewable data.</p>
                </article>
                <article class="rounded-3xl bg-white p-6 shadow-soft">
                    <div class="grid h-12 w-12 place-items-center rounded-2xl bg-emerald-100 text-xl text-emerald-600">$</div>
                    <h3 class="mt-5 text-xl font-semibold">Real-time Analytics</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-600">Dashboard cards summarize reservations, users, pending approvals, and upcoming events.</p>
                </article>
                <article class="rounded-3xl bg-white p-6 shadow-soft">
                    <div class="grid h-12 w-12 place-items-center rounded-2xl bg-amber-100 text-xl text-amber-600">#</div>
                    <h3 class="mt-5 text-xl font-semibold">Client Management</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-600">Admins can approve requests while clients can review reservations in a clean interface.</p>
                </article>
            </div>
        </section>

        <!-- Event Categories -->
        <section id="packages" class="bg-white py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="text-3xl font-semibold tracking-tight text-dark sm:text-4xl">Choose Your Event Type</h2>
                    <p class="mt-4 text-lg text-slate-600">Select the perfect event package for your celebration.</p>
                </div>

                <div class="mt-10 grid gap-6 lg:grid-cols-2">
                    <article class="overflow-hidden rounded-3xl border border-purple-100 bg-soft shadow-sm transition hover:-translate-y-1 hover:shadow-soft">
                        <div class="h-64 overflow-hidden bg-purple-100">
                            <img src="assets/images/wedding.jpg" alt="Wedding event package" class="h-full w-full object-cover">
                        </div>
                        <div class="p-6">
                            <h3 class="text-2xl font-semibold text-dark">Wedding</h3>
                            <p class="mt-3 leading-7 text-slate-600">Create an elegant celebration with planning support for venues, guest coordination, styling, and premium services.</p>
                            <button type="button" data-auth-modal-open="register" class="mt-7 inline-flex w-full justify-center rounded-2xl bg-gradient-to-r from-primary to-secondary px-5 py-3 text-sm font-semibold text-white shadow-soft">
                                Choose Event
                            </button>
                        </div>
                    </article>

                    <article class="overflow-hidden rounded-3xl border border-purple-100 bg-soft shadow-sm transition hover:-translate-y-1 hover:shadow-soft">
                        <div class="h-64 overflow-hidden bg-purple-100">
                            <img src="assets/images/birthday.jpg" alt="Birthday event package" class="h-full w-full object-cover">
                        </div>
                        <div class="p-6">
                            <h3 class="text-2xl font-semibold text-dark">Birthday</h3>
                            <p class="mt-3 leading-7 text-slate-600">Plan a fun and memorable birthday with decoration, sound, catering, lighting, and reservation tracking in one place.</p>
                            <button type="button" data-auth-modal-open="register" class="mt-7 inline-flex w-full justify-center rounded-2xl border border-primary px-5 py-3 text-sm font-semibold text-primary hover:bg-primary hover:text-white">
                                View Details
                            </button>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <!-- Final CTA -->
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-[2rem] bg-gradient-to-r from-primary to-secondary p-8 text-white shadow-soft md:p-12">
                <div class="grid gap-8 md:grid-cols-[1fr_auto] md:items-center">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-white/75">Ready to transform your next event?</p>
                        <h2 class="mt-3 max-w-2xl text-3xl font-semibold tracking-tight sm:text-4xl">Join Eventify and manage reservations with confidence.</h2>
                    </div>
                    <button type="button" data-auth-modal-open="register" class="inline-flex justify-center rounded-2xl bg-white px-6 py-3 text-sm font-semibold text-primary hover:bg-purple-50">Get Started for Free</button>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-purple-100 bg-white">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 text-sm text-slate-600 sm:px-6 md:grid-cols-4 lg:px-8">
            <div class="md:col-span-2">
                <div class="flex items-center gap-3 font-semibold text-dark">
                    <span class="grid h-9 w-9 place-items-center rounded-2xl bg-primary text-white">E</span>
                    Eventify
                </div>
                <p class="mt-4 max-w-sm">The capstone-ready event management system for modern planners and clients.</p>
            </div>
            <div>
                <h3 class="font-semibold text-dark">Product</h3>
                <a class="mt-3 block hover:text-primary" href="#features">Features</a>
                <a class="mt-2 block hover:text-primary" href="#packages">Pricing</a>
                <button type="button" class="mt-2 block hover:text-primary" data-auth-modal-open="login">Client Portal</button>
            </div>
            <div>
                <h3 class="font-semibold text-dark">Company</h3>
                <a class="mt-3 block hover:text-primary" href="#features">About</a>
                <a class="mt-2 block hover:text-primary" href="#packages">Packages</a>
                <button type="button" class="mt-2 block hover:text-primary" data-auth-modal-open="register">Contact</button>
            </div>
        </div>
        <div class="border-t border-purple-100 py-5 text-center text-xs text-slate-500">
            &copy; 2026 Eventify. All rights reserved.
        </div>
    </footer>

    <!-- Login/Register modal -->
    <div id="authModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-dark/60 px-4 py-6 backdrop-blur-sm">
        <div class="mx-auto w-full max-w-2xl rounded-[2rem] bg-white p-6 shadow-soft sm:p-8" role="dialog" aria-modal="true">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-primary">Eventify Access</p>
                    <h2 class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl">Welcome Back</h2>
                </div>
                <button type="button" data-auth-modal-close class="rounded-xl px-3 py-2 font-semibold text-primary hover:bg-purple-50">Close</button>
            </div>

            <div class="mt-6 grid grid-cols-2 rounded-2xl bg-indigo-50 p-1">
                <button type="button" data-auth-tab="login" class="rounded-xl bg-white py-3 font-semibold text-primary shadow-sm">Login</button>
                <button type="button" data-auth-tab="register" class="rounded-xl py-3 font-semibold text-slate-500">Register</button>
            </div>

            <section class="mt-6" data-auth-panel="login">
                <form method="POST" action="../auth/login.php" class="space-y-5">
                    <?php echo eventify_csrf_field(); ?>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Email Address</label>
                        <input type="email" name="email" placeholder="admin@eventify.com" required class="mt-2 w-full rounded-2xl border border-purple-100 bg-indigo-50 px-4 py-4 outline-none focus:border-primary focus:bg-white focus:ring-4 focus:ring-purple-100">
                    </div>

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Password</label>
                        <div class="relative mt-2">
                            <input id="homeLoginPassword" type="password" name="password" placeholder="Password" required class="w-full rounded-2xl border border-purple-100 bg-indigo-50 px-4 py-4 pr-16 outline-none focus:border-primary focus:bg-white focus:ring-4 focus:ring-purple-100">
                            <button type="button" data-password-toggle data-target="homeLoginPassword" class="absolute right-3 top-1/2 -translate-y-1/2 rounded-xl px-3 py-2 text-sm font-bold text-primary hover:bg-purple-50">Show</button>
                        </div>
                    </div>

                    <button type="submit" class="w-full rounded-2xl bg-gradient-to-r from-primary to-secondary px-5 py-4 font-semibold text-white shadow-soft">Sign In</button>
                    <p class="text-center text-sm text-slate-500">Default admin: admin@eventify.com / admin123</p>
                </form>
            </section>

            <section class="mt-6 hidden" data-auth-panel="register">
                <form method="POST" action="../auth/register.php" class="grid gap-4 sm:grid-cols-2">
                    <?php echo eventify_csrf_field(); ?>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Full Name</label>
                        <input type="text" name="name" placeholder="Full Name" required class="mt-2 w-full rounded-2xl border border-purple-100 bg-indigo-50 px-4 py-4 outline-none focus:border-primary focus:bg-white focus:ring-4 focus:ring-purple-100">
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Username</label>
                        <input type="text" name="username" placeholder="Username" required class="mt-2 w-full rounded-2xl border border-purple-100 bg-indigo-50 px-4 py-4 outline-none focus:border-primary focus:bg-white focus:ring-4 focus:ring-purple-100">
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Email</label>
                        <input type="email" name="email" placeholder="Email Address" required class="mt-2 w-full rounded-2xl border border-purple-100 bg-indigo-50 px-4 py-4 outline-none focus:border-primary focus:bg-white focus:ring-4 focus:ring-purple-100">
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Contact Number</label>
                        <input type="text" name="contact" placeholder="Contact Number" required class="mt-2 w-full rounded-2xl border border-purple-100 bg-indigo-50 px-4 py-4 outline-none focus:border-primary focus:bg-white focus:ring-4 focus:ring-purple-100">
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Password</label>
                        <div class="relative mt-2">
                            <input id="homeRegisterPassword" type="password" name="password" placeholder="Password" required class="w-full rounded-2xl border border-purple-100 bg-indigo-50 px-4 py-4 pr-16 outline-none focus:border-primary focus:bg-white focus:ring-4 focus:ring-purple-100">
                            <button type="button" data-password-toggle data-target="homeRegisterPassword" class="absolute right-3 top-1/2 -translate-y-1/2 rounded-xl px-3 py-2 text-sm font-bold text-primary hover:bg-purple-50">Show</button>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Confirm Password</label>
                        <div class="relative mt-2">
                            <input id="homeRegisterConfirm" type="password" name="confirm" placeholder="Confirm Password" required class="w-full rounded-2xl border border-purple-100 bg-indigo-50 px-4 py-4 pr-16 outline-none focus:border-primary focus:bg-white focus:ring-4 focus:ring-purple-100">
                            <button type="button" data-password-toggle data-target="homeRegisterConfirm" class="absolute right-3 top-1/2 -translate-y-1/2 rounded-xl px-3 py-2 text-sm font-bold text-primary hover:bg-purple-50">Show</button>
                        </div>
                    </div>
                    <button type="submit" class="sm:col-span-2 rounded-2xl bg-gradient-to-r from-primary to-secondary px-5 py-4 font-semibold text-white shadow-soft">Create Account</button>
                </form>
            </section>
        </div>
    </div>

    <?php echo eventify_sweetalert_flash(); ?>
    <script src="assets/js/homepage.js"></script>
</body>
</html>
