# Quick Setup: Install FFmpeg on macOS

## Install FFmpeg using Homebrew

```bash
# If you don't have Homebrew, install it first:
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Install FFmpeg
brew install ffmpeg

# Verify installation
ffmpeg -version
```

## Alternative: MacPorts

```bash
sudo port install ffmpeg
```

## Verify It Works

After installation, test with:

```bash
ffmpeg -version
```

You should see output like:
```
ffmpeg version 6.x.x
```

## Then Test Preview Generation

Once FFmpeg is installed, upload a new DJ video through the admin interface, and the system will automatically:

1. Store the original video
2. Generate a smaller preview video (~90% size reduction)
3. Create a poster/thumbnail image
4. Use the preview for display in the table

## Without FFmpeg

The system will work without FFmpeg, but:
- No preview videos will be generated
- Original videos will be used (larger file sizes)
- No poster images will be created
- Performance will be lower but functional

A warning will be logged: `FFmpeg not available, skipping video preview generation`
