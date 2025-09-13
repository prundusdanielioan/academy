# Google OAuth Setup Instructions

## 🔧 Configurarea necesară în .env

Actualizează următoarele variabile în fișierul `.env`:

### Pentru Development Local:

**⚠️ IMPORTANT**: Google OAuth nu acceptă domenii locale (`.local`, `.test`) în configurația de producție. Folosește una din soluțiile de mai jos:

#### Opțiunea 1: localhost (Recomandat)
```env
APP_URL=http://localhost:8000
GOOGLE_CLIENT_ID=your_actual_google_client_id
GOOGLE_CLIENT_SECRET=your_actual_google_client_secret
# GOOGLE_REDIRECT_URI este generat automat din APP_URL + /auth/google/callback
```

#### Opțiunea 2: ngrok pentru domenii locale
```env
APP_URL=http://lms.local:8000
GOOGLE_CLIENT_ID=your_actual_google_client_id
GOOGLE_CLIENT_SECRET=your_actual_google_client_secret
GOOGLE_REDIRECT_URI=https://your-ngrok-url.ngrok-free.app/auth/google/callback
```

### Pentru Production:
```env
APP_URL=https://yourdomain.com
GOOGLE_CLIENT_ID=your_actual_google_client_id
GOOGLE_CLIENT_SECRET=your_actual_google_client_secret
# GOOGLE_REDIRECT_URI este generat automat din APP_URL + /auth/google/callback
```

**Notă**: `GOOGLE_REDIRECT_URI` se generează automat din `APP_URL + /auth/google/callback`. Dacă vrei să folosești un URL diferit, poți seta manual `GOOGLE_REDIRECT_URI`.

## 📋 Pași pentru configurarea Google OAuth

### 1. Creează un proiect Google Cloud Console
1. Mergi la [Google Cloud Console](https://console.cloud.google.com/)
2. Creează un proiect nou sau selectează unul existent
3. Activează Google+ API

### 2. Configurează OAuth consent screen
1. În meniul din stânga, mergi la "APIs & Services" > "OAuth consent screen"
2. Selectează "External" pentru tipul de aplicație
3. Completează informațiile necesare:
   - App name: "LMS Platform"
   - User support email: email-ul tău
   - Developer contact: email-ul tău

### 3. Creează credențiale OAuth 2.0
1. Mergi la "APIs & Services" > "Credentials"
2. Click "Create Credentials" > "OAuth 2.0 Client IDs"
3. Selectează "Web application"
4. Configurează:
   - Name: "LMS Platform OAuth"
   - Authorized JavaScript origins: 
     - `http://localhost:8000` (pentru development localhost)
     - `https://your-ngrok-url.ngrok-free.app` (pentru development cu ngrok)
     - `https://yourdomain.com` (pentru production)
   - Authorized redirect URIs: 
     - `http://localhost:8000/auth/google/callback` (pentru development localhost)
     - `https://your-ngrok-url.ngrok-free.app/auth/google/callback` (pentru development cu ngrok)
     - `https://yourdomain.com/auth/google/callback` (pentru production)

**⚠️ NOTĂ**: Nu folosi domenii locale (`.local`, `.test`) în Google Cloud Console - vor genera eroarea "Invalid Origin: must end with a public top-level domain"

### 4. Copiază credențialele
1. După crearea clientului, copiază:
   - Client ID
   - Client Secret
2. Actualizează fișierul `.env` cu aceste valori

## 🧪 Testarea funcționalității

După configurare:
1. Rulează `php artisan serve`
2. Mergi la `http://localhost:8000/login`
3. Click pe "Sign in with Google"
4. Ar trebui să funcționeze!

## 🚀 Pentru producție

Pentru deploy pe producție:
1. **Actualizează `APP_URL`** cu URL-ul real al aplicației (ex: `https://yourdomain.com`)
2. **GOOGLE_REDIRECT_URI se generează automat** din `APP_URL + /auth/google/callback`
3. **Adaugă URL-urile de producție în Google Cloud Console**:
   - Authorized JavaScript origins: `https://yourdomain.com`
   - Authorized redirect URIs: `https://yourdomain.com/auth/google/callback`

## 🔄 Configurație automată

Aplicația folosește configurația inteligentă:
- **Development**: `APP_URL=http://lms.local:8000` → `GOOGLE_REDIRECT_URI=http://lms.local:8000/auth/google/callback`
- **Production**: `APP_URL=https://yourdomain.com` → `GOOGLE_REDIRECT_URI=https://yourdomain.com/auth/google/callback`

Nu mai trebuie să setezi manual `GOOGLE_REDIRECT_URI` în majoritatea cazurilor!

## 🚨 Troubleshooting

### Eroarea "Invalid Origin: must end with a public top-level domain"

**Cauza**: Ai folosit un domeniu local (`.local`, `.test`) în Google Cloud Console.

**Soluții**:
1. **Folosește localhost** (recomandat):
   ```env
   APP_URL=http://localhost:8000
   ```
   Și în Google Cloud Console: `http://localhost:8000`

2. **Folosește ngrok** pentru domenii locale:
   ```bash
   ngrok http 8000
   ```
   Apoi folosește URL-ul ngrok în Google Cloud Console.

3. **Folosește un subdomeniu real** (ex: `dev.yourdomain.com`)

### Eroarea "redirect_uri_mismatch"

**Cauza**: URL-ul de redirect din aplicație nu se potrivește cu cel din Google Cloud Console.

**Soluție**: Verifică că URL-urile sunt identice în:
- `.env` (sau generat automat din `APP_URL`)
- Google Cloud Console > OAuth 2.0 Client IDs > Authorized redirect URIs

### Eroarea "access_denied"

**Cauza**: Utilizatorul a refuzat permisiunile sau OAuth consent screen nu este configurat.

**Soluție**: 
1. Verifică OAuth consent screen în Google Cloud Console
2. Adaugă email-ul tău în "Test users" dacă aplicația este în modul test
