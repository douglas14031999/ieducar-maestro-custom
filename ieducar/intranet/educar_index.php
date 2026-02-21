<?php

return new class {
    public function RenderHTML()
    {
        $matriculas = \Illuminate\Support\Facades\DB::table('pmieducar.matricula')->count();
        $alunos = \Illuminate\Support\Facades\DB::table('pmieducar.aluno')->count();
        $escolas = \Illuminate\Support\Facades\DB::table('pmieducar.escola')->count();

        return '
            <script src="https://unpkg.com/lucide@latest"></script>
            <style>
                @import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap");
                
                #maestro-app {
                    all: unset;
                    display: block;
                    font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
                    background-color: #fcfcfc !important;
                    min-height: 100vh !important;
                    padding: 32px !important;
                    color: #334155 !important;
                    box-sizing: border-box !important;
                }

                #maestro-app * { box-sizing: border-box !important; }

                .m-container {
                    max-width: 1200px !important;
                    margin: 0 auto !important;
                }

                /* Header */
                .m-header {
                    margin-bottom: 40px !important;
                }

                .m-subtitle {
                    color: #184e7f !important;
                    text-transform: uppercase !important;
                    letter-spacing: 0.1em !important;
                    font-weight: 800 !important;
                    font-size: 12px !important;
                    margin-bottom: 4px !important;
                }

                .m-title {
                    font-size: 32px !important;
                    font-weight: 700 !important;
                    color: #1e293b !important;
                    margin: 0 !important;
                }

                /* Grid System */
                .m-grid {
                    display: grid !important;
                    grid-template-columns: repeat(3, 1fr) !important;
                    gap: 24px !important;
                    margin-bottom: 48px !important;
                }

                /* Cards: Clean Modern */
                .m-card {
                    background: #ffffff !important;
                    border: 1px solid #e2e8f0 !important;
                    border-radius: 8px !important;
                    padding: 32px !important;
                    text-align: center !important;
                    transition: all 0.2s ease-in-out !important;
                    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
                }

                .m-card:hover {
                    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
                    border-color: #184e7f !important;
                }

                .m-icon-box {
                    width: 64px !important;
                    height: 64px !important;
                    background: #eff6ff !important;
                    color: #184e7f !important;
                    border-radius: 50% !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    margin: 0 auto 20px auto !important;
                    transition: all 0.2s !important;
                }

                .m-card:hover .m-icon-box {
                    background: #184e7f !important;
                    color: #ffffff !important;
                }

                .m-stat-label {
                    font-size: 12px !important;
                    font-weight: 600 !important;
                    text-transform: uppercase !important;
                    letter-spacing: 0.05em !important;
                    color: #64748b !important;
                    margin-bottom: 8px !important;
                }

                .m-stat-value {
                    font-size: 36px !important;
                    font-weight: 800 !important;
                    color: #1e293b !important;
                    margin: 0 !important;
                }

                /* Quick Access */
                .m-section-title {
                    font-size: 18px !important;
                    font-weight: 700 !important;
                    color: #334155 !important;
                    margin-bottom: 24px !important;
                    display: flex !important;
                    align-items: center !important;
                    gap: 12px !important;
                }

                .m-section-title::after {
                    content: "" !important;
                    height: 1px !important;
                    background: #e2e8f0 !important;
                    flex: 1 !important;
                }

                .m-nav-grid {
                    display: grid !important;
                    grid-template-columns: repeat(4, 1fr) !important;
                    gap: 20px !important;
                }

                .m-nav-card {
                    display: flex !important;
                    flex-direction: column !important;
                    align-items: center !important;
                    text-decoration: none !important;
                    background: #ffffff !important;
                    border: 1px solid #e2e8f0 !important;
                    border-radius: 8px !important;
                    padding: 24px !important;
                    transition: all 0.2s !important;
                }

                .m-nav-card:hover {
                    background: #f8fafc !important;
                    border-color: #cbd5e1 !important;
                    transform: translateY(-2px) !important;
                }

                .m-nav-icon {
                    margin-bottom: 12px !important;
                    color: #64748b !important;
                    transition: all 0.2s !important;
                }

                .m-nav-card:hover .m-nav-icon {
                    color: #184e7f !important;
                }

                .m-nav-label {
                    font-size: 14px !important;
                    font-weight: 600 !important;
                    color: #475569 !important;
                }

                .m-nav-card:hover .m-nav-label {
                    color: #184e7f !important;
                }

                @media (max-width: 768px) {
                    .m-grid { grid-template-columns: 1fr !important; }
                    .m-nav-grid { grid-template-columns: repeat(2, 1fr) !important; }
                    .m-title { font-size: 24px !important; }
                }
            </style>

            <div id="maestro-app">
                <div class="m-container">
                    

                    <section class="m-grid">
                        <div class="m-card">
                            <div class="m-icon-box"><i data-lucide="users" style="width: 28px; height: 28px;"></i></div>
                            <div class="m-stat-label">Total de Alunos</div>
                            <div class="m-stat-value">' . $alunos . '</div>
                        </div>

                        <div class="m-card">
                            <div class="m-icon-box"><i data-lucide="file-check" style="width: 28px; height: 28px;"></i></div>
                            <div class="m-stat-label">Matrículas Ativas</div>
                            <div class="m-stat-value">' . $matriculas . '</div>
                        </div>

                        <div class="m-card">
                            <div class="m-icon-box"><i data-lucide="school" style="width: 28px; height: 28px;"></i></div>
                            <div class="m-stat-label">Unidades Escolares</div>
                            <div class="m-stat-value">' . $escolas . '</div>
                        </div>
                    </section>

                    <h3 class="m-section-title">Acesso Rápido</h3>
                    
                    <nav class="m-nav-grid">
                        <a href="educar_aluno_cad.php" class="m-nav-card">
                            <div class="m-nav-icon"><i data-lucide="user-plus" style="width: 24px; height: 24px;"></i></div>
                            <span class="m-nav-label">Novos Alunos</span>
                        </a>
                        <a href="educar_matricula_cad.php" class="m-nav-card">
                            <div class="m-nav-icon"><i data-lucide="clipboard-list" style="width: 24px; height: 24px;"></i></div>
                            <span class="m-nav-label">Nova Matrícula</span>
                        </a>
                        <a href="educar_falta_atraso_cad.php" class="m-nav-card">
                            <div class="m-nav-icon"><i data-lucide="edit-3" style="width: 24px; height: 24px;"></i></div>
                            <span class="m-nav-label">Notas e Faltas</span>
                        </a>
                        <a href="educar_servidor_cad.php" class="m-nav-card">
                            <div class="m-nav-icon"><i data-lucide="briefcase" style="width: 24px; height: 24px;"></i></div>
                            <span class="m-nav-label">Servidores</span>
                        </a>
                    </nav>

                </div>
            </div>

            <script>
                if (typeof lucide !== "undefined") {
                    lucide.createIcons();
                }
            </script>
        ';
    }

    public function Formular()
    {
        $this->title = 'Dashboard Institucional';
        $this->processoAp = 55;
    }
};
