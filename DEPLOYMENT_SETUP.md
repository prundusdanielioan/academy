# Deployment Setup pentru cPanel

## Configurarea Secretelor în GitHub

Pentru a face deploy pe cPanel, trebuie să configurezi următoarele secrete în GitHub:

### Pentru SSH Deployment (Recomandat)
1. Mergi la repository-ul tău pe GitHub
2. Click pe **Settings** → **Secrets and variables** → **Actions**
3. Adaugă următoarele secrete:

```
CPANEL_HOST=your-cpanel-host.com
CPANEL_USERNAME=your-cpanel-username
CPANEL_SSH_KEY=your-private-ssh-key
```

### Pentru FTP Deployment (Alternativ)
```
FTP_SERVER=your-ftp-server.com
FTP_USERNAME=your-ftp-username
FTP_PASSWORD=your-ftp-password
CPANEL_HOST=your-cpanel-host.com
CPANEL_USERNAME=your-cpanel-username
CPANEL_SSH_KEY=your-private-ssh-key
```

## Workflow-uri Disponibile

### 1. SSH Deployment (`deploy-cpanel.yml`)
- Creează un pachet de deployment
- Transferă prin SCP
- Extrage și configurează pe server prin SSH
- Rulează migrările și cache-ul

### 2. FTP Deployment (`deploy-ftp.yml`)
- Transferă fișierele prin FTP
- Configurează prin SSH după transfer
- Include build-ul asset-urilor Vite

## Configurarea SSH Key

1. Generează o pereche de chei SSH:
```bash
ssh-keygen -t rsa -b 4096 -C "your-email@example.com"
```

2. Adaugă cheia publică în cPanel:
   - Mergi la **SSH Access** în cPanel
   - Adaugă cheia publică în **Public Keys**

3. Adaugă cheia privată ca secret în GitHub:
   - Copiază conținutul fișierului `~/.ssh/id_rsa`
   - Adaugă-l ca `CPANEL_SSH_KEY`

## Configurarea .env pe Server

Asigură-te că fișierul `.env` pe server conține:

```env
APP_NAME=LMS
APP_ENV=production
APP_KEY=base64:your-generated-key
APP_DEBUG=false
APP_URL=https://self-learn.ro

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=your_database_name
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password

# Alte configurări necesare...
```

## Triggering Deployment

Deployment-ul se va declanșa automat când:
- Faci push pe branch-ul `main` sau `master`
- Rulezi manual workflow-ul din tab-ul **Actions** în GitHub

## Verificarea Log-urilor

Log-urile deployment-ului se găsesc în:
- **GitHub**: Repository → **Actions** tab → Selectează workflow-ul → Vezi log-urile
- **cPanel**: File Manager → `public_html/self-learn.ro` → Verifică fișierele

## Troubleshooting

### Probleme comune:
1. **SSH Connection Failed**: Verifică cheia SSH și host-ul
2. **Permission Denied**: Verifică permisiunile pentru `storage` și `bootstrap/cache`
3. **Database Connection**: Verifică credențialele din `.env`
4. **Composer Issues**: Verifică că PHP și extensiile sunt instalate pe server
