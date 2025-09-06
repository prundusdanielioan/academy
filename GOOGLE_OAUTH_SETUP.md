# Google OAuth Setup Instructions

This application now supports Google OAuth authentication. Follow these steps to set it up:

## 1. Create Google OAuth Credentials

1. Go to the [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Enable the Google+ API
4. Go to "Credentials" in the left sidebar
5. Click "Create Credentials" → "OAuth 2.0 Client IDs"
6. Choose "Web application" as the application type
7. Add authorized redirect URIs:
   - For development: `http://lms.local:8000/auth/google/callback`
   - For production: `https://yourdomain.com/auth/google/callback`

## 2. Configure Environment Variables

Add these variables to your `.env` file:

```env
GOOGLE_CLIENT_ID=your_google_client_id_here
GOOGLE_CLIENT_SECRET=your_google_client_secret_here
GOOGLE_REDIRECT_URI=http://lms.local:8000/auth/google/callback
```

## 3. Features

- **Login with Google**: Users can sign in using their Google accounts
- **Register with Google**: New users can create accounts using Google
- **Account Linking**: Existing users can link their Google accounts
- **Profile Integration**: Google profile information (name, email, avatar) is automatically imported
- **Email Verification**: Google-authenticated users are automatically email-verified

## 4. User Experience

- Users see "Sign in with Google" and "Sign up with Google" buttons on login/register pages
- Clicking the button redirects to Google's OAuth consent screen
- After authorization, users are redirected back to the application and logged in
- Google profile pictures are stored as user avatars
- Users can still use traditional email/password authentication alongside Google OAuth

## 5. Security Notes

- Google OAuth tokens are handled securely by Laravel Socialite
- User passwords are randomly generated for OAuth users (not used for login)
- All OAuth flows use HTTPS in production
- User data is only stored locally after explicit consent

## 6. Testing

To test the Google OAuth functionality:

1. Ensure your `.env` file has the correct Google OAuth credentials
2. Visit `/login` or `/register`
3. Click "Sign in with Google" or "Sign up with Google"
4. Complete the Google OAuth flow
5. Verify you're logged in and redirected to the dashboard
