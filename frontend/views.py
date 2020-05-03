from django.shortcuts import render


def index(request):
    return render(request, 'frontend/index.html')

def explore_text(request):
    return render(request, 'frontend/explore-text.html')

def explore_audio(request):
    return render(request, 'frontend/explore-audio.html')

def explore_video(request):
    return render(request, 'frontend/explore-video.html')

def about(request):
    return render(request, 'frontend/about.html')