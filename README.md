# Sistema de Gestão de Redes

Sistema de gestão para redes de lojas, desenvolvido com Laravel 11, PHP 8.2 e MySQL. Este sistema permite o gerenciamento de grupos econômicos, bandeiras, unidades e colaboradores.

## 🚀 Funcionalidades

- **Gestão de Grupos Econômicos**
  - CRUD completo de grupos econômicos
  - Relacionamento hierárquico com bandeiras

- **Gestão de Bandeiras**
  - CRUD completo de bandeiras
  - Vinculação a grupos econômicos
  - Validação de unicidade de nome por grupo

- **Gestão de Unidades**
  - CRUD completo de unidades
  - Validação de CNPJ
  - Vinculação a bandeiras

- **Gestão de Colaboradores**
  - CRUD completo de colaboradores
  - Validação de CPF e e-mail
  - Vinculação a unidades

- **Autenticação e Autorização**
  - Sistema de login/logout
  - Controle de acesso baseado em permissões
  - Proteção de rotas

- **Interface Moderna**
  - Design responsivo
  - Feedback visual para ações do usuário
  - Componentes reutilizáveis

## 🛠️ Tecnologias Utilizadas

- **Backend**
  - PHP 8.2
  - Laravel 11
  - MySQL 8.0+
  - Laravel Breeze (Autenticação)
  - Spatie Laravel Permission (Controle de Acesso)

- **Frontend**
  - Tailwind CSS
  - Alpine.js
  - Livewire (para componentes interativos)
  - Heroicons

- **Testes**
  - PHPUnit
  - Testes de unidade
  - Testes de integração
  - Testes de recursos

## 📋 Pré-requisitos

- PHP 8.2 ou superior
- Composer
- Node.js e NPM
- MySQL 8.0+ ou MariaDB 10.3+
- Git

## 🚀 Instalação

1. **Clonar o repositório**
   ```bash
   git clone https://github.com/seu-usuario/sistema-gestao.git
   cd sistema-gestao
   ```

2. **Instalar dependências**
   ```bash
   composer install
   npm install
   ```

3. **Configurar ambiente**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configurar banco de dados**
   - Criar um banco de dados MySQL
   - Atualizar as configurações no arquivo `.env`:
     ```
     DB_DATABASE=nome_do_banco
     DB_USERNAME=seu_usuario
     DB_PASSWORD=sua_senha
     ```

5. **Executar migrações e seeders**
   ```bash
   php artisan migrate --seed
   ```

6. **Compilar assets**
   ```bash
   npm run build
   ```

7. **Iniciar servidor de desenvolvimento**
   ```bash
   php artisan serve
   ```

8. **Acessar o sistema**
   - URL: http://localhost:8000
   - Usuário padrão: admin@example.com
   - Senha: password

## 🧪 Executando os Testes

```bash
# Executar todos os testes
php artisan test

# Executar testes específicos
php artisan test --filter=NomeDaClasseDeTeste

# Gerar relatório de cobertura
XDEBUG_MODE=coverage php artisan test --coverage-html=coverage
```

## 🛡️ Segurança

- Validação de entrada em todos os formulários
- Proteção contra CSRF
- Hash de senhas
- Políticas de autorização
- Rate limiting em rotas de autenticação

## 📝 Licença

Este projeto está licenciado sob a licença MIT - veja o arquivo [LICENSE](LICENSE) para detalhes.

## 🤝 Contribuição

1. Faça um Fork do projeto
2. Crie uma Branch para sua Feature (`git checkout -b feature/AmazingFeature`)
3. Adicione suas mudanças (`git add .`)
4. Comite suas mudanças (`git commit -m 'Add some AmazingFeature'`)
5. Faça o Push da Branch (`git push origin feature/AmazingFeature`)
6. Abra um Pull Request

## 📞 Suporte

Para suporte, envie um e-mail para suporte@example.com ou abra uma issue no GitHub.

## 📊 Status do Projeto

🚧 Em desenvolvimento

## 📚 Documentação Adicional

- [Documentação da API](docs/api.md)
- [Guia de Estilo](docs/STYLEGUIDE.md)
- [Guia de Contribuição](docs/CONTRIBUTING.md)
