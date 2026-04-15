---
name: Acervo da Igreja Design System
description: Sistema de design para interface de gestão de acervo eclesiástico
type: design-system
---

# Acervo da Igreja — Design System

## Intenção

**Público:** Equipes de igrejas (pastores, coordenadores, administrativo) + público geral em portal público.

**Sensação:** Acessível, confiável, acolhedor. Como uma sala bem organizada de uma instituição com história. Profissional mas não frio.

**Assinatura:** Banda vitrail (vidraça decorativa) que remete à arquitetura de igrejas.

---

## Fundação

### Espaço de cores

**Semântica:**
- `--surface-base`: #f5efe6 — papel envelhecido, fundo principal (luz)
- `--surface-base-dark`: #0f1014 — charcoal profundo (escuro)
- `--surface-strong`: cor levemente mais densa que base (elevação)
- `--border-subtle`: borda de baixa contraste
- `--border-strong`: borda visível
- `--text-primary`: texto principal, máximo contraste
- `--text-secondary`: texto secundário, intermediário
- `--text-tertiary`: texto suave, terciário

**Identidade:**
- `--accent-primary`: #5b8fd6 — azul calmo, confiança e céu
- `--accent-bronze`: #a87a4a — bronze/ouro quente, arquivo e antiguidade
- `--accent-positive`: #4a9d6f — verde calmo, sucesso
- `--accent-negative`: #d64a4a — vermelho calmo, erro/alerta

### Tipografia

**Fonts:**
- Títulos: Fraunces (serif, presença, arquivo/antiguidade)
- Body: Manrope (sans-serif, legibilidade, moderno)
- Dados/código: JetBrains Mono (monospace, números tabulares)

**Escalas (em rem, base 16px):**
- `h1`: 2rem (32px), weight 700
- `h2`: 1.5rem (24px), weight 700
- `h3`: 1.25rem (20px), weight 600
- `label`: 0.875rem (14px), weight 600
- `body`: 1rem (16px), weight 400
- `small`: 0.875rem (14px), weight 400
- `xs`: 0.75rem (12px), weight 500

### Espaçamento (base 4px)

- `4px` — micro
- `8px` — xs
- `12px` — sm
- `16px` — base (padding padrão)
- `24px` — md (entre seções)
- `32px` — lg (macro-espaçamento)
- `48px` — xl

### Raio

- `4px` — controles (input, button)
- `6px` — cards, popovers
- `8px` — modais
- `20px` — badges, pills

### Profundidade

**Estratégia:** Bordas sutis + mudanças tonais. Sem sombras dramáticas.

Elevações:
- `--elevation-0`: sem borda, mesma cor
- `--elevation-1`: borda sutil (1px, baixa opacidade)
- `--elevation-2`: borda visível (1px) + tom levemente mais claro/escuro

---

## Padrões de Componentes

### Topbar (cabeçalho)

```
├── Brand lockup (logo + título)
├── Nav links (desktop, hidden mobile)
├── Actions (theme toggle, auth button)
└── Vitrail band (rodapé do header, assinatura)
```

**Estado:**
- Fundo base (papel)
- Borda sutil inferior
- Sem sombra
- Altura fixa: ~64px
- Padding: 16px
- Nav links com underline animado em hover/active

### Mobile Menu Shell

**Ativa em <1024px (lg breakpoint):**
- Input checkbox hidden (toggle)
- Backdrop escuro translúcido
- Painel slide-in da esquerda
- Fechado por padrão, animação smooth de 220ms
- Overflow scroll se conteúdo maior que viewport

### Nav Links

- Sem underline por padrão
- Estado active: cor accent + border-bottom
- Hover: cor accent levemente mais clara
- Transição: 120ms ease
- Mobile: block, espaçamento vertical

### Botões

**Primary:**
- Fundo: accent-primary
- Texto: branco
- Padding: 12px 24px
- Altura: 40px (em containers com espaçamento)
- Raio: 4px (em controles), 20px (pills)
- Border: nenhuma

**Muted:**
- Fundo: surface-strong
- Texto: text-primary
- Border: border-subtle
- Hover: background levemente mais escuro

**Estados:** hover (10% mais escuro), active, disabled (opacity 50%), focus (outline 2px)

### Theme Toggle

- Botão com 2 ícones (sun/moon)
- Display inline-flex
- Apenas um visível por vez via CSS
- Sem texto, apenas ícone

### Guest Shell (layout de login)

- Mesma topbar que app.blade.php
- Body em coluna, centrada
- Fundo: surface-base
- Padding: 24px+ nos lados

### Vitrail Band

- Altura: 5px (ou 8px)
- Background: padrão gradiente ou cor sólida bronze/accent
- Posição: rodapé do cabeçalho
- Serve como detalhe visual/assinatura

---

## Grid e Layout

### Breakpoints

- `lg`: 1024px — onde nav muda de hidden para flex

### Page Frame

- `space-y-6`: espaçamento vertical entre seções
- Max-width: sem limite (full-width é ok)
- Padding ajustado por screen size

### Panel Padding

- Desktop: 24px
- Mobile: 16px

---

## Modo Escuro

Em `data-theme="dark"`:
- Inverta cores de base/strong
- Aumente raio visual com bordas mais visíveis
- Mantenha assinatura (vitrail band) em cor quente
- Text-primary: branco/off-white
- Preserve tipografia e espaçamento

---

## Decisões e Rejeitadas

**Rejeitadas:**
1. Template genérico de dashboard → Construído específico para fluxo de navegação
2. Sombras em camadas → Usar bordas sutis (mais limpo em modo escuro)
3. Nav vertical fixa → Nav horizontal (desktop) + mobile modal (mais fluido)

---

Last updated: 2026-04-08
