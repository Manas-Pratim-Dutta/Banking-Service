<footer class="border-t border-zinc-300 pt-2 pb-6 px-4 bg-white">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-1">
            <div class="hidden sm:block">
                <img src="{{ asset('images/nic-logo.png') }}" alt="NIC Logo" class="h-16 w-auto">
            </div>

            <div class="text-center text-sm text-zinc-700 leading-relaxed">
                <p>
                    Site designed and developed by
                    <a href="http://www.nic.in/" target="_blank" class="text-primary font-medium hover:underline">
                        National Informatics Centre
                    </a>
                </p>
                <p>
                    Contents provided and maintained by
                    Revenue &amp; D.M. (Registration) Department, Govt of Assam
                </p>
            </div>

            <div class="hidden sm:block">
                <img src="{{ asset('images/digital-india.png') }}" alt="DI Logo" class="h-10 w-auto">
            </div>
        </div>

        <div class="mt-1 border-t border-zinc-300"></div>
    </div>
</footer>

@stack('page-scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/marked/9.1.6/marked.min.js"></script>
</body>
</html>