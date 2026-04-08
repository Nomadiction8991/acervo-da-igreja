# Runbook do Napkin

## Regras de Curadoria
- Repriorize a cada leitura.
- Mantenha apenas notas recorrentes e de alto valor.
- Maximo de 10 itens por categoria.
- Cada item inclui data + "Faca isto em vez disso".

## Execucao e Validacao
1. **[2026-04-01] Bootstrap de aplicacao em repositorio Git vazio**
   Faca isto em vez disso: gere o scaffold em uma pasta temporaria e copie os arquivos para a raiz, preservando o `.git/`.

## Shell e Confiabilidade
1. **[2026-04-01] `rg` nao esta disponivel neste ambiente**
   Faca isto em vez disso: use `find`, `git ls-files` e comandos POSIX equivalentes para inspecao rapida.
2. **[2026-04-01] Apache com bind mount falha se a raiz do projeto nao for executavel**
   Faca isto em vez disso: garanta `chmod 755` na raiz montada e trate permissoes de `storage/` e `bootstrap/cache/` no entrypoint do container.
3. **[2026-04-01] Laravel em bind mount Linux quebra logs e cache quando o Apache roda com UID diferente do dono da pasta**
   Faca isto em vez disso: remapeie o `www-data` para o UID/GID detectado em `/var/www/html`, toque `storage/logs/laravel.log` no startup e reaplique permissoes de `storage/` e `bootstrap/cache/`.

## Regras do Dominio
1. **[2026-04-01] Docker padrao para Laravel**
   Faca isto em vez disso: prefira Laravel Sail como stack inicial de desenvolvimento, salvo pedido explicito por composicao manual.

## Diretrizes do Usuario
