-- Cria o banco de dados (se já não existir)
CREATE DATABASE IF NOT EXISTS nocode_hub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nocode_hub;

-- Tabela de Projetos
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aluno_nome VARCHAR(255) NOT NULL,
    aluno_email VARCHAR(255) NOT NULL,
    aluno_nivel ENUM('Iniciante', 'Intermediário', 'Avançado') NOT NULL,
    projeto_nome VARCHAR(255) NOT NULL,
    projeto_descricao TEXT NOT NULL,
    projeto_objetivo VARCHAR(255) NOT NULL,
    ferramentas_usadas VARCHAR(255) NOT NULL,
    integracoes VARCHAR(255) NOT NULL,
    em_producao TINYINT(1) DEFAULT 0,
    horas_economizadas_semana DECIMAL(5,2) DEFAULT 0.00,
    area_ajuda VARCHAR(100) NOT NULL,
    link_referencia VARCHAR(255) DEFAULT NULL,
    
    -- Campos da IA
    nivel_projeto VARCHAR(50) DEFAULT NULL,
    area_principal VARCHAR(100) DEFAULT NULL,
    stack_sugerida TEXT DEFAULT NULL,
    tags_ai TEXT DEFAULT NULL,
    impacto_estimado VARCHAR(50) DEFAULT NULL,
    dificuldade_manutencao VARCHAR(50) DEFAULT NULL,
    resumo_curto TEXT DEFAULT NULL,
    proxima_aula_recomendada TEXT DEFAULT NULL,
    
    -- Controle Interno
    status_board ENUM('Ideia', 'Construção', 'Testes', 'Produção') DEFAULT 'Ideia',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de Administradores
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Inserir usuário admin padrão (Senha: admin123)
-- Hash gerado com password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO admin_users (nome, email, senha_hash) VALUES 
('Mentor Principal', 'admin@admin.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
