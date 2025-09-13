# Google OAuth Environment Configuration Examples

## 🔧 Configurații pentru diferite medii

### Development Local (lms.local) - ⚠️ PROBLEMATIC
```env
# ⚠️ NU FOLOSI - Google OAuth nu acceptă domenii locale
APP_URL=http://lms.local:8000
GOOGLE_CLIENT_ID=your_google_client_id_here
GOOGLE_CLIENT_SECRET=your_google_client_secret_here
# GOOGLE_REDIRECT_URI will be auto-generated as: http://lms.local:8000/auth/google/callback
# ❌ Va genera eroarea: "Invalid Origin: must end with a public top-level domain"
```

### Development Local (localhost) - ✅ RECOMANDAT
```env
APP_URL=http://localhost:8000
GOOGLE_CLIENT_ID=your_google_client_id_here
GOOGLE_CLIENT_SECRET=your_google_client_secret_here
# GOOGLE_REDIRECT_URI will be auto-generated as: http://localhost:8000/auth/google/callback
# ✅ Funcționează perfect cu Google OAuth
```

### Development cu ngrok (pentru domenii locale)
```env
APP_URL=http://lms.local:8000
GOOGLE_CLIENT_ID=your_google_client_id_here
GOOGLE_CLIENT_SECRET=your_google_client_secret_here
GOOGLE_REDIRECT_URI=https://abc123.ngrok-free.app/auth/google/callback
# ✅ Folosește ngrok pentru a expune domeniul local
```

### Production
```env
APP_URL=https://yourdomain.com
GOOGLE_CLIENT_ID=your_google_client_id_here
GOOGLE_CLIENT_SECRET=your_google_client_secret_here
# GOOGLE_REDIRECT_URI will be auto-generated as: https://yourdomain.com/auth/google/callback
```

### Custom redirect URI (if needed)
```env
APP_URL=https://yourdomain.com
GOOGLE_CLIENT_ID=your_google_client_id_here
GOOGLE_CLIENT_SECRET=your_google_client_secret_here
GOOGLE_REDIRECT_URI=https://custom-domain.com/auth/google/callback
```

## 🎯 Configurația actuală

Pentru configurația ta actuală cu `lms.local`, folosește:

```env
APP_URL=http://lms.local:8000
GOOGLE_CLIENT_ID=your_actual_google_client_id
GOOGLE_CLIENT_SECRET=your_actual_google_client_secret
# Șterge sau comentează GOOGLE_REDIRECT_URI pentru a folosi auto-generarea
```

## 🔄 Cum funcționează auto-generarea

1. **Dacă `GOOGLE_REDIRECT_URI` nu este setat** → se folosește `APP_URL + /auth/google/callback`
2. **Dacă `GOOGLE_REDIRECT_URI` este setat** → se folosește valoarea manuală
3. **Pentru production** → doar schimbi `APP_URL` și totul se actualizează automat

## ✅ Avantaje

- **Simplu**: Nu trebuie să setezi manual redirect URI-ul
- **Flexibil**: Funcționează pentru development și production
- **Sigur**: Nu uiți să actualizezi redirect URI-ul la deploy
- **Consistent**: Folosește întotdeauna APP_URL ca bază
