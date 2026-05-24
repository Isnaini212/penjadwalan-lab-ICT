
<x-layouts.public title="Login">

    <div class="min-h-screen flex items-center justify-center px-4">

        <div class="w-full max-w-md bg-white rounded-2xl shadow-sm border border-slate-200 p-8">

            <div class="text-center">
                <h1 class="text-3xl font-bold text-slate-900">
                    Login Admin
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    Masuk untuk mengelola jadwal laboratorium.
                </p>
            </div>

            <form class="mt-8 space-y-5">

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Email
                    </label>

                    <input
                        type="email"
                        placeholder="Masukkan email"
                        class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-400"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Password
                    </label>

                    <input
                        type="password"
                        placeholder="Masukkan password"
                        class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-400"
                    >
                </div>

                <button
                    type="submit"
                    class="w-full py-3 rounded-xl bg-slate-900 text-white font-medium hover:bg-slate-700 transition"
                >
                    Login
                </button>

            </form>

        </div>

    </div>


</x-layout.public>
