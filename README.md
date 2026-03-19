# Anthropic API Provider for Moodle

## Overview

The Anthropic API Provider plugin integrates Anthropic's Claude AI models into Moodle's AI framework, enabling powerful AI capabilities across your learning management system. This plugin supports text generation, text summarization, and text explanation using Anthropic's state-of-the-art Claude models.

## Features

### Supported AI Actions

- **Text Generation**: Generate creative and informative text responses using Claude models
- **Text Summarization**: Automatically summarize long text content for better comprehension
- **Explain Text**: Ask the AI to explain complex text in simpler terms

### Key Features

- **Dynamic Model Discovery**: Automatically fetches available Claude models from the Anthropic API with intelligent caching
- **Multiple Model Support**: Choose from various Claude models (Opus, Sonnet, Haiku) or specify a custom model
- **Privacy-First**: No personal data is stored locally; user identification is anonymized
- **Flexible Configuration**: Customizable system instructions, API endpoints, and model parameters
- **Moodle Integration**: Seamlessly integrates with Moodle's core AI framework

## Installation

### Prerequisites

- Moodle 4.5+
- PHP 8.0 or higher
- Valid Anthropic API key

### Installation Steps

1. **Download the Plugin**
   - Download the plugin from the [Moodle plugins directory](https://moodle.org/plugins/view/aiprovider_anthropic)
   - Or clone from the GitHub repository

2. **Install the Plugin**
   - Extract the plugin to your Moodle installation's `/ai/provider/` directory
   - The final path should be: `/ai/provider/anthropic/`

3. **Install via Moodle Admin**
   - Log in to your Moodle site as an administrator
   - Navigate to **Site administration > Notifications**
   - Follow the installation prompts

4. **Configure the Plugin**
   - Go to **Site administration > Plugins > AI providers > Anthropic API provider**
   - Enter your Anthropic API key
   - Configure model and rate limiting settings as needed

## Configuration

### API Key Setup

1. **Get an Anthropic API Key**
   - Visit [Claude Console API Keys](https://platform.claude.com/settings/keys)
   - Create a new API key
   - Copy the key to your clipboard

2. **Configure in Moodle**
   - Navigate to **Site administration > Plugins > AI providers > Anthropic API provider**
   - Paste your API key in the "Anthropic API key" field
   - Save changes

### Model Configuration

#### Text Generation / Summarization / Explanation Models
- **Default**: First available model from API discovery
- **Fallback Models**: Claude Opus 4.6, Claude Sonnet 4.6, Claude Haiku 4.5
- **Custom Models**: Specify any model name not in the predefined list
- **System Instructions**: Customizable prompts for consistent AI behavior

#### Model Parameters
- **max_tokens** (default: 2048): Maximum number of tokens to generate
- **temperature**: Amount of randomness injected into the response
- **top_p**: Nucleus sampling parameter
- **top_k**: Top-K sampling parameter
- **Extra Parameters**: JSON-formatted additional parameters for advanced use cases

## Usage

### For Administrators

1. **Enable AI Features**
   - Navigate to **Site administration > Plugins > AI providers**
   - Enable the Anthropic API provider
   - Configure global settings

2. **Monitor Usage**
   - Check rate limiting status in the admin interface
   - Monitor API usage through the Anthropic Console dashboard

### For Teachers

1. **Access AI Features**
   - AI features are available in supported activities and resources
   - Look for AI-powered options in content creation tools

## Troubleshooting

### Common Issues

#### API Key Errors
- **Problem**: Provider not configured or API errors
- **Solution**: Verify your API key is correctly entered in the plugin settings

#### Model Loading Issues
- **Problem**: Models not appearing in dropdown
- **Solution**: Verify your API key has access to the required models; the plugin will fall back to a static model list if the API is unreachable

### Debug Information

Enable debugging in Moodle to get detailed error information:
1. Go to **Site administration > Development > Debugging**
2. Enable debugging and set appropriate levels
3. Check the debug output for detailed error messages

## Support

### Getting Help

- **Documentation**: This README and inline help in Moodle
- **Issue Tracker**: [GitHub Issues](https://github.com/lucademichelirubio/moodle-aiprovider_anthropic/issues)

### Contributing

We welcome contributions! Please:
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## License

This plugin is licensed under the [GNU General Public License v3.0](https://www.gnu.org/licenses/gpl-3.0.html).

## Credits

- **Developer**: Luca Demicheli Rubio (lucademichelirubio.portfolio@gmail.com)
- **Copyright**: 2026 Luca Demicheli Rubio

## See Also

- [Anthropic API Documentation](https://docs.anthropic.com/)
- [Moodle Plugin Development Guide](https://docs.moodle.org/dev/Plugin_types)